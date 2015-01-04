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
use AppBundle\Entity\AvisoParte;
use AppBundle\Entity\Observacion;
use AppBundle\Entity\ObservacionParte;
use AppBundle\Entity\Sancion;
use AppBundle\Entity\TramoParte;
use AppBundle\Entity\Usuario;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="parte")
 */
class Parte
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="partes")
     * @var Usuario
     */
    protected $usuario;
    /**
     * @ORM\ManyToOne(targetEntity="Alumno", inversedBy="partes")
     * @var Alumno
     */
    protected $alumno;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @var Usuario
     */
    protected $profesor_guardia;
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
     * @ORM\Column(type="text")
     * @var string
     */
    protected $actividades;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    protected $fecha_creacion;
    /**
     * @ORM\ManyToOne(targetEntity="TramoParte")
     */
    protected $tramo;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var boolean
     */
    protected $avisado;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $fecha_aviso;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var boolean
     */
    protected $prescrito;
    /**
     * @ORM\ManyToOne(targetEntity="Sancion", inversedBy="partes")
     */
    protected $sancion;
    /**
     * @ORM\OneToMany(targetEntity="ObservacionParte", mappedBy="parte")
     */
    protected $observaciones = null;
    /**
     * @ORM\OneToMany(targetEntity="AvisoParte", mappedBy="parte")
     */
    protected $avisos = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->observaciones = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return Parte
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
     * @return Parte
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
     * Set actividades
     *
     * @param string $actividades
     * @return Parte
     */
    public function setActividades($actividades)
    {
        $this->actividades = $actividades;

        return $this;
    }

    /**
     * Get actividades
     *
     * @return string 
     */
    public function getActividades()
    {
        return $this->actividades;
    }

    /**
     * Set fecha_creacion
     *
     * @param \DateTime $fechaCreacion
     * @return Parte
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fecha_creacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fecha_creacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion()
    {
        return $this->fecha_creacion;
    }

    /**
     * Set avisado
     *
     * @param integer $avisado
     * @return Parte
     */
    public function setAvisado($avisado)
    {
        $this->avisado = $avisado;

        return $this;
    }

    /**
     * Get avisado
     *
     * @return integer 
     */
    public function getAvisado()
    {
        return $this->avisado;
    }

    /**
     * Set fecha_aviso
     *
     * @param \DateTime $fechaAviso
     * @return Parte
     */
    public function setFechaAviso($fechaAviso)
    {
        $this->fecha_aviso = $fechaAviso;

        return $this;
    }

    /**
     * Get fecha_aviso
     *
     * @return \DateTime 
     */
    public function getFechaAviso()
    {
        return $this->fecha_aviso;
    }

    /**
     * Set usuario
     *
     * @param Usuario $usuario
     * @return Parte
     */
    public function setUsuario(Usuario $usuario = null)
    {
        $this->usuario = $usuario;

        return $this;
    }

    /**
     * Get usuario
     *
     * @return Usuario
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * Set alumno
     *
     * @param Alumno $alumno
     * @return Parte
     */
    public function setAlumno(Alumno $alumno = null)
    {
        $this->alumno = $alumno;

        return $this;
    }

    /**
     * Get alumno
     *
     * @return Alumno
     */
    public function getAlumno()
    {
        return $this->alumno;
    }

    /**
     * Set profesor_guardia
     *
     * @param Usuario $profesorGuardia
     * @return Parte
     */
    public function setProfesorGuardia(Usuario $profesorGuardia = null)
    {
        $this->profesor_guardia = $profesorGuardia;

        return $this;
    }

    /**
     * Get profesor_guardia
     *
     * @return Usuario
     */
    public function getProfesorGuardia()
    {
        return $this->profesor_guardia;
    }

    /**
     * Set sancion
     *
     * @param Sancion $sancion
     * @return Parte
     */
    public function setSancion(Sancion $sancion = null)
    {
        $this->sancion = $sancion;

        return $this;
    }

    /**
     * Get sancion
     *
     * @return Sancion
     */
    public function getSancion()
    {
        return $this->sancion;
    }

    /**
     * Add observaciones
     *
     * @param ObservacionParte $observaciones
     * @return Parte
     */
    public function addObservacion(ObservacionParte $observaciones)
    {
        $this->observaciones[] = $observaciones;

        return $this;
    }

    /**
     * Remove observaciones
     *
     * @param ObservacionParte $observaciones
     */
    public function removeObservacion(ObservacionParte $observaciones)
    {
        $this->observaciones->removeElement($observaciones);
    }

    /**
     * Get observaciones
     *
     * @return Collection
     */
    public function getObservaciones()
    {
        return $this->observaciones;
    }

    /**
     * Add avisos
     *
     * @param AvisoParte $avisos
     * @return Parte
     */
    public function addAviso(AvisoParte $avisos)
    {
        $this->avisos[] = $avisos;

        return $this;
    }

    /**
     * Remove avisos
     *
     * @param AvisoParte $avisos
     */
    public function removeAviso(AvisoParte $avisos)
    {
        $this->avisos->removeElement($avisos);
    }

    /**
     * Get avisos
     *
     * @return Collection
     */
    public function getAvisos()
    {
        return $this->avisos;
    }

    /**
     * Set prescrito
     *
     * @param boolean $prescrito
     * @return Parte
     */
    public function setPrescrito($prescrito)
    {
        $this->prescrito = $prescrito;

        return $this;
    }

    /**
     * Get prescrito
     *
     * @return boolean 
     */
    public function getPrescrito()
    {
        return $this->prescrito;
    }

    /**
     * Set tramo
     *
     * @param TramoParte $tramo
     * @return Parte
     */
    public function setTramo(TramoParte $tramo = null)
    {
        $this->tramo = $tramo;

        return $this;
    }

    /**
     * Get tramo
     *
     * @return TramoParte
     */
    public function getTramo()
    {
        return $this->tramo;
    }
}
