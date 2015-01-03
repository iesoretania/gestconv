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

use AppBundle\Entity\Alumno;
use AppBundle\Entity\AvisoSancion;
use AppBundle\Entity\Medida;
use AppBundle\Entity\Parte;
use AppBundle\Entity\Usuario;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="sancion")
 */
class Sancion
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="sanciones")
     * @var Usuario
     */
    protected $usuario;
    /**
     * @ORM\Column(type="integer", nullable=false)
     * @var int
     */
    protected $tipo;
    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    protected $anotacion;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    protected $fecha_sancion;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var bool
     */
    protected $comunicado;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $fecha_comunicado;
    /**
     * @ORM\OneToMany(targetEntity="Parte", mappedBy="sancion")
     */
    protected $partes = null;
    /**
     * @ORM\OneToMany(targetEntity="Medida", mappedBy="parte")
     */
    protected $medidas = null;
    /**
     * @ORM\OneToMany(targetEntity="AvisoSancion", mappedBy="sancion")
     */
    protected $avisos = null;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->partes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->medidas = new \Doctrine\Common\Collections\ArrayCollection();
        $this->avisos = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tipo
     *
     * @param integer $tipo
     * @return Sancion
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return integer 
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set anotacion
     *
     * @param string $anotacion
     * @return Sancion
     */
    public function setAnotacion($anotacion)
    {
        $this->anotacion = $anotacion;

        return $this;
    }

    /**
     * Get anotacion
     *
     * @return string 
     */
    public function getAnotacion()
    {
        return $this->anotacion;
    }

    /**
     * Set fecha_sancion
     *
     * @param \DateTime $fechaSancion
     * @return Sancion
     */
    public function setFechaSancion($fechaSancion)
    {
        $this->fecha_sancion = $fechaSancion;

        return $this;
    }

    /**
     * Get fecha_sancion
     *
     * @return \DateTime 
     */
    public function getFechaSancion()
    {
        return $this->fecha_sancion;
    }

    /**
     * Set comunicado
     *
     * @param boolean $comunicado
     * @return Sancion
     */
    public function setComunicado($comunicado)
    {
        $this->comunicado = $comunicado;

        return $this;
    }

    /**
     * Get comunicado
     *
     * @return boolean 
     */
    public function getComunicado()
    {
        return $this->comunicado;
    }

    /**
     * Set fecha_comunicado
     *
     * @param \DateTime $fechaComunicado
     * @return Sancion
     */
    public function setFechaComunicado($fechaComunicado)
    {
        $this->fecha_comunicado = $fechaComunicado;

        return $this;
    }

    /**
     * Get fecha_comunicado
     *
     * @return \DateTime 
     */
    public function getFechaComunicado()
    {
        return $this->fecha_comunicado;
    }

    /**
     * Set usuario
     *
     * @param \AppBundle\Entity\Usuario $usuario
     * @return Sancion
     */
    public function setUsuario(\AppBundle\Entity\Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return \AppBundle\Entity\Usuario 
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Add partes
     *
     * @param \AppBundle\Entity\Parte $partes
     * @return Sancion
     */
    public function addParte(\AppBundle\Entity\Parte $partes)
    {
        $this->partes[] = $partes;

        return $this;
    }

    /**
     * Remove partes
     *
     * @param \AppBundle\Entity\Parte $partes
     */
    public function removeParte(\AppBundle\Entity\Parte $partes)
    {
        $this->partes->removeElement($partes);
    }

    /**
     * Get partes
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPartes()
    {
        return $this->partes;
    }

    /**
     * Add medidas
     *
     * @param \AppBundle\Entity\Medida $medidas
     * @return Sancion
     */
    public function addMedida(\AppBundle\Entity\Medida $medidas)
    {
        $this->medidas[] = $medidas;

        return $this;
    }

    /**
     * Remove medidas
     *
     * @param \AppBundle\Entity\Medida $medidas
     */
    public function removeMedida(\AppBundle\Entity\Medida $medidas)
    {
        $this->medidas->removeElement($medidas);
    }

    /**
     * Get medidas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedidas()
    {
        return $this->medidas;
    }

    /**
     * Add avisos
     *
     * @param \AppBundle\Entity\AvisoSancion $avisos
     * @return Sancion
     */
    public function addAviso(\AppBundle\Entity\AvisoSancion $avisos)
    {
        $this->avisos[] = $avisos;

        return $this;
    }

    /**
     * Remove avisos
     *
     * @param \AppBundle\Entity\AvisoSancion $avisos
     */
    public function removeAviso(\AppBundle\Entity\AvisoSancion $avisos)
    {
        $this->avisos->removeElement($avisos);
    }

    /**
     * Get avisos
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAvisos()
    {
        return $this->avisos;
    }
}
