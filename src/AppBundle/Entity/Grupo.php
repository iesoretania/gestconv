<?php
/*
  GESTCONV - Aplicación web para la gestión de la convivencia en centros educativos

  Copyright (C) 2015: Luis Ramón López López

  This program is free software: you can redistribute it and/or modify
  it under the terms of the GNU Affero General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU Affero General Public License for more details.

  You should have received a copy of the GNU Affero General Public License
  along with this program.  If not, see [http://www.gnu.org/licenses/].
*/

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Curso;
use AppBundle\Entity\Alumno;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Grupo
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    
    /**
     * @ORM\Column(type="string")
     * @var string
     */
    protected $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity="Curso")
     * @var Curso
     */
    protected $curso;
    
    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @var Usuario
     */
    protected $tutor;

    /**
     * @ORM\OneToMany(targetEntity="Alumno", mappedBy="grupo")
     * @var Alumno[]
     */
    protected $alumnado=null;

    public function __construct() {
        $this->alumnado = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     *
     * @return string
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    
    /**
     *
     * @param string $valor
     * @return Grupo
     */
    public function setDescripcion($valor)
    {
        $this->descripcion = $valor;

        return $this;
    }
    
    /**
     *
     * @return Curso
     */
    public function getCurso()
    {
        return $this->curso;
    }

    public function setCurso($curso)
    {
        $curso->incorporarGrupo($this);
        $this->curso = $curso;
    }
    
    /**
     *
     * @return Curso
     */
    public function getTutor()
    {
        return $this->tutor;
    }
    
    /**
     *
     * @param Usuario $tutor
     * @return Grupo
     */
    public function setTutor($tutor)
    {
        $tutor->asignarTutoria($this);
        $this->tutor = $tutor;

        return $this;
    }


    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->descripcion;
    }

    /**
     * Add alumnado
     *
     * @param Alumno $alumnado
     * @return Grupo
     */
    public function addAlumnado(Alumno $alumnado)
    {
        $this->alumnado[] = $alumnado;

        return $this;
    }

    /**
     * Remove alumnado
     *
     * @param Alumno $alumnado
     */
    public function removeAlumnado(Alumno $alumnado)
    {
        $this->alumnado->removeElement($alumnado);
    }

    /**
     * Get alumnado
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAlumnado()
    {
        return $this->alumnado;
    }
}
