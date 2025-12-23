<?php

/**
 * Model - Clase Base Abstracta para Modelos
 *
 * Proporciona funcionalidad común para todos los modelos del sistema.
 * Permite extraer propiedades y procesar query strings de forma segura.
 *
 * FUNCIONALIDADES:
 * - Extracción selectiva de propiedades del modelo
 * - Procesamiento de query strings
 * - Validación de existencia de propiedades
 *
 * USO:
 * Los modelos específicos deben extender esta clase y definir:
 * - Propiedades públicas para datos estáticos
 * - Procesos para query strings dinámicos
 *
 * @file Model.class.php
 * @package Model
 */

namespace Model;

abstract class Model {

    /**
     * Extrae propiedades específicas del modelo
     *
     * Recorre un array de nombres de propiedades y extrae solo aquellas
     * que existen en el modelo actual. Útil para obtener datos selectivos
     * sin exponer todas las propiedades del modelo.
     *
     * @param array $data Array de nombres de propiedades a extraer
     * @return array Array asociativo [propiedad => valor]
     *
     * @example
     * // En un modelo con propiedades: $usuario, $fecha, $permisos
     * $model->fetchData(['usuario', 'fecha']);
     * // Retorna: ['usuario' => 'John', 'fecha' => '2024-01-01']
     */
    public function fetchData($data) {
        $response = [];
        foreach ($data as $value) {
            if (property_exists($this, $value)) {
                $response[$value] = $this->$value;
            }
        }
        return $response;
    }

    /**
     * Procesa query strings mediante método de modelo
     *
     * Ejecuta método para procesar el conjunto de llaves y valores
     * de el o los query strings para la ruta.
     *
     * @param mixed $data Array de query strings a pasar al método
     * @return array Resultado del método
     */
    public function fetchQueryStringData($data) {
        $response = [];
        $response = $this->queryStringHandler($data);
        return $response;
    }
}
