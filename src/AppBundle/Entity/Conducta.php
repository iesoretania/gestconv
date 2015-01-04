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

use AppBundle\Entity\Parte;
use AppBundle\Entity\TipoConducta;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class Conducta
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Parte", inversedBy="conductas")
     * @var Parte
     */
    protected $parte;

    /**
     * @ORM\ManyToOne(targetEntity="TipoConducta")
     * @var TipoConducta
     */
    protected $tipo;

    /**
     * @ORM\Column(type="text")
     * @var string
     */
    protected $anotacion;

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
     * @return Conducta
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
     * Set parte
     *
     * @param Parte $parte
     * @return Conducta
     */
    public function setParte(Parte $parte = null)
    {
        $this->parte = $parte;

        return $this;
    }

    /**
     * Get parte
     *
     * @return Parte
     */
    public function getParte()
    {
        return $this->parte;
    }

    /**
     * Set tipo
     *
     * @param TipoConducta $tipo
     * @return Conducta
     */
    public function setTipo(TipoConducta $tipo = null)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return TipoConducta
     */
    public function getTipo()
    {
        return $this->tipo;
    }
}
