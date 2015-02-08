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


use AppBundle\Validator\Constraints\DateRange as AssertDateRange;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ParteRepository")
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
     *
     * @Assert\NotBlank()
     */
    protected $usuario;
    /**
     * @ORM\ManyToOne(targetEntity="Alumno", inversedBy="partes")
     * @ORM\JoinColumn(nullable=false)
     * @var Alumno
     */
    protected $alumno;
    /**
     * @ORM\ManyToOne(targetEntity="Usuario")
     * @var Usuario
     * @Assert\NotBlank(groups={"expulsion"}, message="parte.profesor-guardia.expulsion")
     */
    protected $profesorGuardia;
    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     *
     * @Assert\Length(min="10", minMessage="parte.anotacion.min_length")
     */
    protected $anotacion;
    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     *
     * @Assert\NotBlank(groups={"expulsion"}, message="parte.actividades.expulsion")
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
     *
     * @Assert\NotBlank(groups={"nuevo"}, message="parte.fecha_suceso.not_blank")
     * @AssertDateRange(max="+10 minutes", maxMessage="parte.fecha_suceso.max")
     */
    protected $fechaSuceso;
    /**
     * @ORM\ManyToOne(targetEntity="TramoParte")
     * @ORM\JoinColumn(nullable=false)
     * @var TramoParte
     *
     * @Assert\NotBlank(groups={"nuevo"}, message="parte.tramo.not_blank")
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
     * @ORM\Column(type="boolean", nullable=false)
     * @var boolean
     */
    protected $hayExpulsion;
    /**
     * @ORM\ManyToMany(targetEntity="TipoConducta")
     * @var Collection
     *
     * @Assert\Count(min="1", minMessage="parte.conductas.min")
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
     * Set hayExpulsion
     *
     * @param boolean $hayExpulsion
     * @return Parte
     */
    public function setHayExpulsion($hayExpulsion)
    {
        $this->hayExpulsion = $hayExpulsion;

        return $this;
    }

    /**
     * Get hayExpulsion
     *
     * @return boolean
     */
    public function getHayExpulsion()
    {
        return $this->hayExpulsion;
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
     * @param TipoConducta $conductas
     * @return Parte
     */
    public function addConducta(TipoConducta $conductas)
    {
        $this->conductas[] = $conductas;

        return $this;
    }

    /**
     * Remove conductas
     *
     * @param TipoConducta $conductas
     */
    public function removeConducta(TipoConducta $conductas)
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

}
