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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\SancionRepository")
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
     * @ORM\JoinColumn(nullable=false)
     * @var Usuario
     */
    protected $usuario;
    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     */
    protected $anotacion;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $fechaSancion;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $fechaComunicado;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $fechaRegistro;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Blank(groups={"sin_sancion"}, message="sancion.sin_sancion.fecha")
     * @var \DateTime
     */
    protected $fechaInicioSancion;
    /**
     * @ORM\Column(type="date", nullable=true)
     * @Assert\Expression(expression="(this.getFechaInicioSancion() == null and this.getFechaFinSancion() == null) or (this.getFechaInicioSancion() != null and this.getFechaFinSancion() >= this.getFechaInicioSancion())", message="sancion.fecha_fin_sancion.min")
     * @Assert\Blank(groups={"sin_sancion"}, message="sancion.sin_sancion.fecha")
     * @var \DateTime
     */
    protected $fechaFinSancion;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var boolean
     */
    protected $medidasEfectivas;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @var boolean
     */
    protected $reclamacion;
    /**
     * @ORM\Column(type="text", nullable=true)
     * @var string
     *
     * @Assert\NotBlank(groups={"sin_sancion"}, message="sancion.sin_sancion.motivos")
     */
    protected $motivosNoAplicacion;
    /**
     * @ORM\ManyToOne(targetEntity="ActitudFamiliaSancion")
     * @var ActitudFamiliaSancion
     */
    protected $actitudFamilia;
    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var boolean
     */
    protected $registradoEnSeneca;
    /**
     * @ORM\OneToMany(targetEntity="Parte", mappedBy="sancion")
     * @var Collection
     *
     * @Assert\Count(min="1", minMessage="sancion.partes.min")
     */
    protected $partes = null;
    /**
     * @ORM\ManyToMany(targetEntity="TipoMedida")
     * @var Collection
     *
     * @Assert\Count(min="1", minMessage="sancion.medidas.min")
     */
    protected $medidas = null;
    /**
     * @ORM\OneToMany(targetEntity="AvisoSancion", mappedBy="sancion")
     * @var Collection
     */
    protected $avisos = null;
    /**
     * @ORM\OneToMany(targetEntity="ObservacionSancion", mappedBy="sancion")
     * @var Collection
     */
    protected $observaciones = null;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->partes = new ArrayCollection();
        $this->medidas = new ArrayCollection();
        $this->avisos = new ArrayCollection();
        $this->observaciones = new ArrayCollection();
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
     * Set fechaSancion
     *
     * @param \DateTime $fechaSancion
     * @return Sancion
     */
    public function setFechaSancion($fechaSancion)
    {
        $this->fechaSancion = $fechaSancion;

        return $this;
    }

    /**
     * Get fechaSancion
     *
     * @return \DateTime 
     */
    public function getFechaSancion()
    {
        return $this->fechaSancion;
    }

    /**
     * Set fechaComunicado
     *
     * @param \DateTime $fechaComunicado
     * @return Sancion
     */
    public function setFechaComunicado($fechaComunicado)
    {
        $this->fechaComunicado = $fechaComunicado;

        return $this;
    }

    /**
     * Get fechaComunicado
     *
     * @return \DateTime
     */
    public function getFechaComunicado()
    {
        return $this->fechaComunicado;
    }

    /**
     * Set fechaRegistro
     *
     * @param \DateTime $fechaRegistro
     * @return Sancion
     */
    public function setFechaRegistro($fechaRegistro)
    {
        $this->fechaRegistro = $fechaRegistro;

        return $this;
    }

    /**
     * Get fechaRegistro
     *
     * @return \DateTime
     */
    public function getFechaRegistro()
    {
        return $this->fechaRegistro;
    }

    /**
     * Set usuario
     *
     * @param Usuario $usuario
     * @return Sancion
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
     * Add partes
     *
     * @param Parte $partes
     * @return Sancion
     */
    public function addParte(Parte $partes)
    {
        $this->partes[] = $partes;

        return $this;
    }

    /**
     * Remove partes
     *
     * @param Parte $partes
     */
    public function removeParte(Parte $partes)
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
     * @param TipoMedida $medidas
     * @return Sancion
     */
    public function addMedida(TipoMedida $medidas)
    {
        $this->medidas[] = $medidas;

        return $this;
    }

    /**
     * Remove medidas
     *
     * @param TipoMedida $medidas
     */
    public function removeMedida(TipoMedida $medidas)
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
     * @param AvisoSancion $avisos
     * @return Sancion
     */
    public function addAviso(AvisoSancion $avisos)
    {
        $this->avisos[] = $avisos;

        return $this;
    }

    /**
     * Remove avisos
     *
     * @param AvisoSancion $avisos
     */
    public function removeAviso(AvisoSancion $avisos)
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

    /**
     * Set medidasEfectivas
     *
     * @param boolean $medidasEfectivas
     * @return Sancion
     */
    public function setMedidasEfectivas($medidasEfectivas)
    {
        $this->medidasEfectivas = $medidasEfectivas;

        return $this;
    }

    /**
     * Get registradoEnSeneca
     *
     * @return boolean 
     */
    public function getRegistradoEnSeneca()
    {
        return $this->registradoEnSeneca;
    }


    /**
     * Set registradoEnSeneca
     *
     * @param boolean $registradoEnSeneca
     * @return Sancion
     */
    public function setRegistradoEnSeneca($registradoEnSeneca)
    {
        $this->registradoEnSeneca = $registradoEnSeneca;

        return $this;
    }

    /**
     * Get medidasEfectivas
     *
     * @return boolean
     */
    public function getMedidasEfectivas()
    {
        return $this->medidasEfectivas;
    }

    /**
     * Set reclamacion
     *
     * @param boolean $reclamacion
     * @return Sancion
     */
    public function setReclamacion($reclamacion)
    {
        $this->reclamacion = $reclamacion;

        return $this;
    }

    /**
     * Get reclamacion
     *
     * @return boolean 
     */
    public function getReclamacion()
    {
        return $this->reclamacion;
    }

    /**
     * Set motivosNoAplicacion
     *
     * @param string $motivosNoAplicacion
     * @return Sancion
     */
    public function setMotivosNoAplicacion($motivosNoAplicacion)
    {
        $this->motivosNoAplicacion = $motivosNoAplicacion;

        return $this;
    }

    /**
     * Get motivosNoAplicacion
     *
     * @return string 
     */
    public function getMotivosNoAplicacion()
    {
        return $this->motivosNoAplicacion;
    }

    /**
     * Set actitudFamilia
     *
     * @param ActitudFamiliaSancion $actitudFamilia
     * @return Sancion
     */
    public function setActitudFamilia(ActitudFamiliaSancion $actitudFamilia = null)
    {
        $this->actitudFamilia = $actitudFamilia;

        return $this;
    }

    /**
     * Get actitudFamilia
     *
     * @return ActitudFamiliaSancion
     */
    public function getActitudFamilia()
    {
        return $this->actitudFamilia;
    }

    /**
     * Add observaciones
     *
     * @param ObservacionSancion $observaciones
     * @return Sancion
     */
    public function addObservacion(ObservacionSancion $observaciones)
    {
        $this->observaciones[] = $observaciones;

        return $this;
    }

    /**
     * Remove observaciones
     *
     * @param ObservacionSancion $observaciones
     */
    public function removeObservacion(ObservacionSancion $observaciones)
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
     * Set fechaInicioSancion
     *
     * @param \DateTime $fechaInicioSancion
     * @return Sancion
     */
    public function setFechaInicioSancion($fechaInicioSancion)
    {
        $this->fechaInicioSancion = $fechaInicioSancion;

        return $this;
    }

    /**
     * Get fechaInicioSancion
     *
     * @return \DateTime 
     */
    public function getFechaInicioSancion()
    {
        return $this->fechaInicioSancion;
    }

    /**
     * Set fechaFinSancion
     *
     * @param \DateTime $fechaFinSancion
     * @return Sancion
     */
    public function setFechaFinSancion($fechaFinSancion)
    {
        $this->fechaFinSancion = $fechaFinSancion;

        return $this;
    }

    /**
     * Get fechaFinSancion
     *
     * @return \DateTime 
     */
    public function getFechaFinSancion()
    {
        return $this->fechaFinSancion;
    }

}
