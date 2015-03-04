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

namespace AppBundle\Form\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Importar
{
    /**
     * @Assert\File
     * @var UploadedFile
     */
    protected $fichero;

    /**
     * @var boolean
     */
    protected $eliminar;

    /**
     * @var boolean
     */
    protected $actualizar;

    /**
     * @return UploadedFile
     */
    public function getFichero()
    {
        return $this->fichero;
    }

    /**
     * @param UploadedFile $fichero
     *
     * @return self
     */
    public function setFichero(UploadedFile $fichero)
    {
        $this->fichero = $fichero;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEliminar()
    {
        return $this->eliminar;
    }

    /**
     * @param boolean $eliminar
     */
    public function setEliminar($eliminar)
    {
        $this->eliminar = $eliminar;
    }

    /**
     * @return boolean
     */
    public function getActualizar()
    {
        return $this->actualizar;
    }

    /**
     * @param boolean $actualizar
     */
    public function setActualizar($actualizar)
    {
        $this->actualizar = $actualizar;
    }
}
