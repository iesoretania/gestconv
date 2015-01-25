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
 * @ORM\Entity
 * @ORM\Table
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
     * @ORM\JoinColumn(nullable=false)
     * @var Usuario
     */
    protected $usuario;
    /**
     * @ORM\ManyToMany(targetEntity="Alumno", mappedBy="partes")
     * @var Collection
     */
    protected $alumnos;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @var Usuario
     */
    protected $profesorGuardia;
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
    protected $fechaCreacion;
    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    protected $fechaSuceso;
    /**
     * @ORM\ManyToOne(targetEntity="TramoParte")
     * @ORM\JoinColumn(nullable=false)
     * @var TramoParte
     */
    protected $tramo;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $fechaAviso;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var boolean
     */
    protected $prescrito;
    /**
     * @ORM\OneToMany(targetEntity="Conducta", mappedBy="parte")
     * @var Collection
     */
    protected $conductas = null;
    /**
     * @ORM\ManyToOne(targetEntity="Sancion", inversedBy="partes")
     * @var Sancion
     */
    protected $sancion;
    /**
     * @ORM\OneToMany(targetEntity="ObservacionParte", mappedBy="parte")
     * @var Collection
     */
    protected $observaciones = null;
    /**
     * @ORM\OneToMany(targetEntity="AvisoParte", mappedBy="parte")
     * @var Collection
     */
    protected $avisos = null;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->conductas = new ArrayCollection();
        $this->observaciones = new ArrayCollection();
        $this->avisos = new ArrayCollection();
        $this->alumnos = new ArrayCollection();
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
     * Set fechaCreacion
     *
     * @param \DateTime $fechaCreacion
     * @return Parte
     */
    public function setFechaCreacion($fechaCreacion)
    {
        $this->fechaCreacion = $fechaCreacion;

        return $this;
    }

    /**
     * Get fechaCreacion
     *
     * @return \DateTime 
     */
    public function getFechaCreacion()
    {
        return $this->fechaCreacion;
    }

    /**
     * Set fechaSuceso
     *
     * @param \DateTime $fechaSuceso
     * @return Parte
     */
    public function setFechaSuceso($fechaSuceso)
    {
        $this->fechaSuceso = $fechaSuceso;

        return $this;
    }

    /**
     * Get fechaSuceso
     *
     * @return \DateTime
     */
    public function getFechaSuceso()
    {
        return $this->fechaSuceso;
    }

    /**
     * Set fechaAviso
     *
     * @param \DateTime $fechaAviso
     * @return Parte
     */
    public function setFechaAviso($fechaAviso)
    {
        $this->fechaAviso = $fechaAviso;

        return $this;
    }

    /**
     * Get fechaAviso
     *
     * @return \DateTime 
     */
    public function getFechaAviso()
    {
        return $this->fechaAviso;
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
     * Set profesorGuardia
     *
     * @param Usuario $profesorGuardia
     * @return Parte
     */
    public function setProfesorGuardia(Usuario $profesorGuardia = null)
    {
        $this->profesorGuardia = $profesorGuardia;

        return $this;
    }

    /**
     * Get profesorGuardia
     *
     * @return Usuario
     */
    public function getProfesorGuardia()
    {
        return $this->profesorGuardia;
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

    /**
     * Add conductas
     *
     * @param Conducta $conductas
     * @return Parte
     */
    public function addConducta(Conducta $conductas)
    {
        $this->conductas[] = $conductas;

        return $this;
    }

    /**
     * Remove conductas
     *
     * @param Conducta $conductas
     */
    public function removeConducta(Conducta $conductas)
    {
        $this->conductas->removeElement($conductas);
    }

    /**
     * Get conductas
     *
     * @return Collection
     */
    public function getConductas()
    {
        return $this->conductas;
    }


    /**
     * Add alumnos
     *
     * @param Alumno $alumnos
     * @return Parte
     */
    public function addAlumno(Alumno $alumnos)
    {
        $this->alumnos[] = $alumnos;

        return $this;
    }

    /**
     * Remove alumnos
     *
     * @param Alumno $alumnos
     */
    public function removeAlumno(Alumno $alumnos)
    {
        $this->alumnos->removeElement($alumnos);
    }

    /**
     * Get alumnos
     *
     * @return Collection
     */
    public function getAlumnos()
    {
        return $this->alumnos;
    }
}
