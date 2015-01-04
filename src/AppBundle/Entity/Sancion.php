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

use AppBundle\Entity\ActitudFamiliaParte;
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
     * @var boolean
     */
    protected $comunicado;
    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    protected $fecha_comunicado;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var boolean
     */
    protected $medidasEfectivas;
    /**
     * @ORM\Column(type="boolean", nullable=false)
     * @var boolean
     */
    protected $reclamacion;
    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $motivosNoAplicacion;
    /**
     * @ORM\ManyToOne(targetEntity="ActitudFamiliaSancion")
     */
    protected $actitudFamilia;
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
     * @param Medida $medidas
     * @return Sancion
     */
    public function addMedida(Medida $medidas)
    {
        $this->medidas[] = $medidas;

        return $this;
    }

    /**
     * Remove medidas
     *
     * @param Medida $medidas
     */
    public function removeMedida(Medida $medidas)
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
}
