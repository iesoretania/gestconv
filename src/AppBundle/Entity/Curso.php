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


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CursoRepository")
 */
class Curso
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
     * @ORM\OneToMany(targetEntity="Grupo", mappedBy="curso")
     * @var Collection
     */
    protected $grupos = null;
    
    /**
     *
     */
    public function __construct()
    {
        $this->grupos = new ArrayCollection();
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
     * @return Curso
     */
    public function setDescripcion($valor)
    {
        $this->descripcion = $valor;

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
     * Add grupos
     *
     * @param Grupo $grupos
     * @return Curso
     */
    public function addGrupo(Grupo $grupos)
    {
        $this->grupos[] = $grupos;
        $grupos->setCurso($this);

        return $this;
    }

    /**
     * Remove grupos
     *
     * @param Grupo $grupos
     */
    public function removeGrupo(Grupo $grupos)
    {
        $this->grupos->removeElement($grupos);
    }

    /**
     * Get grupos
     *
     * @return Collection
     */
    public function getGrupos()
    {
        return $this->grupos;
    }
}
