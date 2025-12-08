<?php

/**
 * Model - Clase Base Abstracta para Modelos
 *
 * Proporciona funcionalidad común para todos los modelos del sistema.
 * Permite extraer propiedades y procesar query strings de forma segura.
 *
 * FUNCIONALIDADES:
 * - Extracción selectiva de propiedades del modelo
 * - Procesamiento de query strings mediante métodos del modelo
 * - Validación de existencia de propiedades y métodos
 *
 * USO:
 * Los modelos específicos deben extender esta clase y definir:
 * - Propiedades públicas para datos estáticos
 * - Métodos para procesar query strings dinámicos
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
        foreach ($data as $key => $value) {
            if (property_exists($this, $value)) {
                $response[$value] = $this->$value;
            }
        }
        return $response;
    }

    /**
     * Procesa query strings mediante métodos del modelo
     *
     * Busca un método en el modelo que coincida con $key y lo ejecuta
     * pasándole $value como parámetro. Si el método no existe, redirige
     * a la página principal. Útil para rutas dinámicas.
     *
     * @param string $key Nombre del método a ejecutar
     * @param mixed $value Valor a pasar al método
     * @return array Resultado del método o array vacío
     */
    public function fetchQueryStringData($key,$value) {
        $response = [];
        if (method_exists($this, $key)) {
            $response = $this->$key($value);
        } else {
            header("Location: " . URL_BASE);
        }
        return $response;
    }
}
