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

use AppBundle\Entity\Sancion;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class ObservacionSancion extends Observacion
{
    /**
     * @ORM\ManyToOne(targetEntity="Sancion", inversedBy="observaciones")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $sancion;

    /**
     * Set sancion
     *
     * @param Sancion $sancion
     * @return ObservacionSancion
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
}
