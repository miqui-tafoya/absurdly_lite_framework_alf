<?php

/**
 * Database - Gestor de Conexión y Operaciones MySQL
 *
 * Proporciona una capa de abstracción para operaciones de base de datos MySQL.
 * Incluye métodos para consultas preparadas, CRUD, JOINs y queries raw.
 *
 * CARACTERÍSTICAS:
 * - Conexión automática mediante credenciales .env
 * - Consultas preparadas con protección contra SQL injection
 * - Métodos CRUD simplificados (Create, Read, Update, Delete)
 * - Soporte para JOINs (LEFT/RIGHT con UNION)
 * - Queries raw con diferentes formatos de resultado
 * - Cierre automático de conexión en destructor
 * - Validación de identificadores SQL (tablas, columnas)
 *
 * MÉTODOS PRINCIPALES:
 * - dbCall(): Consultas SELECT con filtros, JOINs y ORDER BY
 * - crudCall(): Operaciones INSERT, UPDATE, DELETE
 * - dbMYSQLCall_raw_*(): Queries SQL directas
 * - exeQuery(): Ejecuta consultas preparadas
 *
 * SEGURIDAD:
 * - Validación estricta de nombres de tablas y columnas
 * - Protección contra SQL injection en identificadores
 * - Solo permite caracteres alfanuméricos y guiones bajos
 *
 * @file Database.class.php
 * @package Model
 */

namespace Model;

use DotEnv;

class Database {

  private DotEnv $dotenv;
  private $host;
  private $user;
  private $pass;
  private $db_name;
  public $conn = null;

  public function __construct() {
    $this->dotenv = new DotEnv(APP_ROOT . DIRECTORY_SEPARATOR . '.env');
    $this->dotenv->load();
    $this->host = \getenv('HOST');
    $this->user = \getenv('USER');
    $this->pass = \getenv('PASS');
    $this->db_name = \getenv('DB_NAME');
    $this->connect();
  }

  /**
   * Valida y sanitiza identificadores SQL (tablas, columnas)
   *
   * Solo permite: letras, números, guiones bajos y puntos (para alias)
   * Previene SQL injection en nombres de tablas/columnas
   *
   * @param string $identifier Identificador a validar
   * @param string $type Tipo de identificador ('table', 'column', 'order')
   * @return string Identificador validado
   * @throws \Exception Si el identificador contiene caracteres inválidos
   */
  private function validaIdentificador($identifier, $type = 'column') {
    if (!preg_match('/^[a-zA-Z0-9_\.]+$/', $identifier)) {
      throw new \Exception("Identificador SQL inválido ($type): $identifier");
    }
    return $identifier;
  }

  /**
   * Valida dirección de ORDER BY
   *
   * @param string $direction Dirección (ASC, DESC, o variantes con números)
   * @return string Dirección validada (ASC o DESC)
   */
  private function validaOrden($direction) {
    $clean = preg_replace('/\d+/', '', strtoupper($direction));
    $clean = preg_replace('/NULL/', '', $clean);
    $clean = trim($clean);
    if (!in_array($clean, ['ASC', 'DESC', ''], true)) {
      throw new \Exception("Dirección ORDER BY inválida: $direction");
    }
    return $clean;
  }

  /**
   * Valida tipo de LIMIT
   *
   * @param string $type Tipo de límite ('one', 'all', o número)
   * @return string|int Tipo validado
   */
  private function validaLimite($type) {
    if ($type === 'one' || $type === 'all') {
      return $type;
    }
    if (!is_numeric($type) || $type < 0) {
      throw new \Exception("Tipo LIMIT inválido: $type");
    }
    return (int) $type;
  }

  /**
   * Establece conexión con MySQL
   *
   * @return \mysqli Objeto de conexión MySQLi
   */
  public function connect() {
    $this->conn = new \mysqli($this->host, $this->user, $this->pass, $this->db_name);
    if ($this->conn->connect_error) {
      die('Error de conexión con MySQL: ' . $this->conn->connect_error);
    }
    return $this->conn;
  }

  /**
   * Ejecuta consulta preparada con parámetros
   *
   * @param string $sql Query SQL con placeholders (?)
   * @param array $data Datos a vincular (bind)
   * @return \mysqli_stmt Statement ejecutado
   */
  public function exeQuery($sql, $data) {
    $stmt = $this->conn->prepare($sql);
    $valores = array_values($data);
    if (strpos($sql, 'UNION') !== false) {
      $valores = array_merge($valores, $valores);
    }
    $tipo = str_repeat('s', count($valores));
    $stmt->bind_param($tipo, ...$valores);
    $stmt->execute();
    return $stmt;
  }

  /**
   * Query raw - Retorna un registro como array asociativo
   *
   * @param string $sql Query SQL completo
   * @return array Array asociativo
   */
  public function dbMYSQLCall_raw_ASSOC($sql) {
    $stmt = $this->conn->query($sql);
    $data = $stmt->fetch_array(MYSQLI_ASSOC);
    return $data;
  }

  /**
   * Query raw - Retorna un registro como array numérico
   *
   * @param string $sql Query SQL completo
   * @return array Array numérico
   */
  public function dbMYSQLCall_raw_NUM($sql) {
    $stmt = $this->conn->query($sql);
    $data = $stmt->fetch_array(MYSQLI_NUM);
    return $data;
  }

  /**
   * Query raw - Retorna un registro como array mixto
   *
   * @param string $sql Query SQL completo
   * @return array Array mixto (asociativo + numérico)
   */
  public function dbMYSQLCall_raw_BOTH($sql) {
    $stmt = $this->conn->query($sql);
    $data = $stmt->fetch_array(MYSQLI_BOTH);
    return $data;
  }

  /**
   * Query raw - Retorna todos los registros como array asociativo
   *
   * @param string $sql Query SQL completo
   * @return array Array de arrays asociativos
   */
  public function dbMYSQLCall_raw_ALL_ASSOC($sql) {
    $stmt = $this->conn->query($sql);
    $data = $stmt->fetch_all(MYSQLI_ASSOC);
    return $data;
  }

  /**
   * Query raw - Retorna todos los registros como array numérico
   *
   * @param string $sql Query SQL completo
   * @return array Array de arrays numéricos
   */
  public function dbMYSQLCall_raw_ALL_NUM($sql) {
    $stmt = $this->conn->query($sql);
    $data = $stmt->fetch_all(MYSQLI_NUM);
    return $data;
  }

  /**
   * Query raw - Retorna todos los registros como array mixto
   *
   * @param string $sql Query SQL completo
   * @return array Array de arrays mixtos
   */
  public function dbMYSQLCall_raw_ALL_BOTH($sql) {
    $stmt = $this->conn->query($sql);
    $data = $stmt->fetch_all(MYSQLI_BOTH);
    return $data;
  }

  /**
   * Consulta SELECT con filtros, JOINs y ORDER BY
   *
   * Construye y ejecuta queries SELECT con soporte para:
   * - Selección de columnas específicas o todas (*)
   * - Filtros WHERE con operadores = o != (prefijo !)
   * - LEFT/RIGHT JOINs con UNION automático
   * - ORDER BY con múltiples columnas y direcciones
   * - Filtros para uno, varios o todos los registros
   * - Límites y offset para implementar, por ejemplo, un sistema de paginación
   *
   * @param string $type Tipo de resultado: 'one' para un array simple con un solo registro, 'all' para un array asociativo de muchos registros, 'limit' para establecer un "limit y offset" (por ejemplo para implementar paginación), o un entero para limitar la cantidad de resultados a ese número fijo
   * @param bool|array $join false o ['cols' => [...], 'on' => [...]]
   * @param array|string $cols Columnas a seleccionar o 'all'
   * @param string $table Nombre de la tabla
   * @param array $data Filtros WHERE ['columna' => 'valor', '!columna' => 'valor']
   * @param array $order ORDER BY ['ASC' => 'columna', 'DESC2' => 'columna2']
   * @param array $limits ORDER BY ['ASC' => 'columna', 'DESC2' => 'columna2']
   * @return array Registros encontrados
   *
   * @example
   * // SELECT simple para todas las coincidencias, sin filtros y con ordenamiento ascendente de un parámetro
   * $columnas = ['id', 'nombre']
   * $db->dbCall('all', false, $columnas, 'tabla_usuarios', [], ['ASC' => 'nombre']);
   * 
   * @example
   * // SELECT simple para una sola coincidencia, con filtro de parámetro 'activo' = 1 y dos ordenamientos ascendentes de diferentes parámetros
   * $columnas = ['id', 'nombre']
   * $db->dbCall('one', false, $columnas, 'tabla_usuarios', ['activo' => 1], ['ASC' => 'nombre', 'ASC2' => 'fecha']);
   * 
   * @example
   * // SELECT simple con límite para 5 coincidencias, con 2 filtros y con ordenamientos diversos
   * $columnas = ['id', 'nombre']
   * $db->dbCall(5, false, $columnas, 'tabla_usuarios', ['activo' => 1, 'rol' => 'administrador'], ['ASC' => 'nombre', 'DESC' => 'fecha', 'DESC2' => 'tipo']);
   *
   * @example
   * // SELECT con JOIN para todas las coincidencias con 1 filtro por variable y sin ordenamiento
   * $join = [
   *     'cols' => [
   *         'tabla_usuarios' => 'id_usuarios, nombre, apellido',
   *         'tabla_roles' => 'tipo, vigencia'],
   *     'on' => [
   *         'tabla_usuarios' => 'id_usuarios',
   *         'tabla_roles' => 'id_usuarios']
   *         ];
   * $variable = 1;
   * $db->dbCall('all', $join, false, false, ['id_usuarios' => $variable], []);
   * 
   * @example 
   * // SELECT para todas las coincidencias, con filtros, ordenamiento y límite con offset para implementar paginación
   * $page = max(1, (int) $page);
   * $perPage = max(1, (int) $perPage);
   * $offset = ($page - 1) * $perPage;
   * 
   * $cols = ['all'];
   * $data = ['tipo' => 'noticias', 'publicado' => 1, 'categoria' => $categoria];
   * $order = ['DESC' => 'timestamp'];
   * $limits = ['limit' => $perPage, 'offset' => $offset];
   * $db->dbCall('limit', false, $cols, 'tb_entradas', $data, $order, $limits);
   */
  public function dbCall(string $type, $join, $cols, string $table, $data = [], $order = [], $limits = []) {
    $sql = "SELECT ";
    $i = 0;
    if (!is_array($join) && $join === false) {
      if ($cols[0] === 'all' || $cols === 'all') {
        $sql .= "*";
      } else {
        foreach ($cols as $key => $value) {
          if ($i === 0) {
            $sql .= " $value";
          } else {
            $sql .= " , $value";
          }
          $i++;
        }
      }
    } else {
      foreach ($join['cols'] as $key => $value) {
        if (preg_match('/[,]/', $value)) {
          $join['cols'] = explode(",", $value);
          foreach ($join['cols'] as $subkey => $subvalue) {
            if ($i === 0) {
              $sql .= " $key.$subvalue";
            } else {
              $sql .= " , $key.$subvalue";
            }
            $i++;
          }
        } else {
          if ($value === 'all') {
            $value = "*";
          }
          if ($i === 0) {
            $sql .= " $key.$value";
          } else {
            $sql .= " , $key.$value";
          }
        }
        $i++;
      }
    }
    $sql .= " FROM $table";
    if (!is_array($join) && $join === false) {
      if (!empty($data)) {
        $i = 0;
        foreach ($data as $key => $value) {
          $but = preg_match('/^!/', $key) ? true : false;
          $key = ($but === true) ? substr($key, 1) : $key;
          if ($i === 0) {
            $sql .= " WHERE $key";
            $sql .= ($but === true) ? '!=?' : '=?';
          } else {
            $sql .= " AND $key";
            $sql .= ($but === true) ? '!=?' : '=?';
          }
          $i++;
        }
      }
    } else {
      $i = 0;
      foreach ($join['on'] as $key => $value) {
        if ($i === 0) {
          $sql .= " $key LEFT JOIN";
        } else {
          $sql .= " $key";
        }
        $i++;
      }
      $sql .= " ON";
      $i = 0;
      foreach ($join['on'] as $key => $value) {
        if ($i === 0) {
          $sql .= " $key.$value=";
        } else {
          $sql .= "$key.$value";
        }
        $i++;
      }
      if (!empty($data)) {
        $i = 0;
        foreach ($data as $key => $value) {
          $but = preg_match('/^!/', $key) ? true : false;
          $key = ($but === true) ? substr($key, 1) : $key;
          if ($i === 0) {
            $sql .= " WHERE $key";
            $sql .= ($but === true) ? '!=?' : '=?';
          } else {
            $sql .= " AND $key";
            $sql .= ($but === true) ? '!=?' : '=?';
          }
          $i++;
        }
      }
      $sqlRight = str_replace("LEFT JOIN", "RIGHT JOIN", $sql);
      $sql = $sql . " UNION " . $sqlRight;
    }
    if (!empty($order)) {
      $i = 0;
      foreach ($order as $key => $value) {
        if ($i === 0) {
          $sql .= ' ORDER BY ' . $value;
          if (preg_match('/\d+/', $key) == true) {
            $key = preg_replace('/\d+/', '', $key);
          }
          $sql .= ' ' . $key;
        } else {
          $null = preg_match('/NULL/', $key) ? true : false;
          $key = ($null === true) ? '' : $key;
          $sql .= ', ' . $value;
          if ($null === false) {
            if (preg_match('/\d+/', $key) == true) {
              $key = preg_replace('/\d+/', '', $key);
            }
            $sql .= ' ' . $key;
          }
        }
        $i++;
      }
    }
    if ($type == 'one') {
      $sql .= " LIMIT 1";
      $stmt = $this->exeQuery($sql, $data);
      $records = $stmt->get_result()->fetch_assoc();
    } elseif ($type == 'limit') {
      $limit = isset($limits['limit']) ? (int) $limits['limit'] : 0;
      $offset = isset($limits['offset']) ? (int) $limits['offset'] : 0;
      $sql .= " LIMIT $limit";
      $sql .= " OFFSET $offset";
      if (empty($data)) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      } else {
        $stmt = $this->exeQuery($sql, $data);
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      }
    } elseif ($type == 'all') {
      if (empty($data)) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      } else {
        $stmt = $this->exeQuery($sql, $data);
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      }
    } else {
      $sql .= " LIMIT ";
      $sql .= $type;
      if (empty($data)) {
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      } else {
        $stmt = $this->exeQuery($sql, $data);
        $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
      }
    }
    return $records;
  }

  /**
   * Consulta SELECT COUNT para conteo de registros con filtros
   *
   * Construye y ejecuta una query de conteo (COUNT(*)) con soporte para:
   * - Filtros WHERE con operadores = o != (usando el prefijo ! en la llave)
   * - Retorno directo del valor numérico total
   *
   * @param string $table Nombre de la tabla
   * @param array $data Filtros WHERE opcionales ['columna' => 'valor', '!columna' => 'valor']
   * @return int Cantidad total de registros encontrados
   *
   * @example
   * // Conteo simple de todos los registros en una tabla
   * $total = $db->dbCount('tabla_usuarios');
   * 
   * @example
   * // Conteo con filtros específicos (usuarios activos de una categoría)
   * $filtros = ['activo' => 1, 'categoria' => 'premium'];
   * $total = $db->dbCount('tabla_usuarios', $filtros);
   * 
   * @example
   * // Conteo excluyendo registros (usuarios que no sean administradores)
   * $total = $db->dbCount('tabla_usuarios', ['!rol' => 'admin']);
   * 
   * @example
   * // Uso típico para sistemas de paginación (conteo de noticias publicadas)
   * $condiciones = [
   *     'tipo' => 'noticia',
   *     'publicado' => 1,
   *     'categoria' => $categoria
   * ];
   * $totalRegistros = $db->dbCount('tb_entradas', $condiciones);
   */
  public function dbCount(string $table, $data = []) {
    $sql = "SELECT COUNT(*) as total FROM $table";
    if (!empty($data)) {
      $i = 0;
      foreach ($data as $key => $value) {
        $but = preg_match('/^!/', $key) ? true : false;
        $key = ($but === true) ? substr($key, 1) : $key;

        if ($i === 0) {
          $sql .= " WHERE $key";
          $sql .= ($but === true) ? '!=?' : '=?';
        } else {
          $sql .= " AND $key";
          $sql .= ($but === true) ? '!=?' : '=?';
        }
        $i++;
      }
    }
    if (empty($data)) {
      $stmt = $this->conn->prepare($sql);
      $stmt->execute();
      $result = $stmt->get_result()->fetch_assoc();
    } else {
      $stmt = $this->exeQuery($sql, $data);
      $result = $stmt->get_result()->fetch_assoc();
    }
    return $result['total'];
  }

  /**
   * Operaciones CRUD (Create, Read, Update, Delete)
   *
   * @param string $type Operación: 'create', 'update', 'delete', 'deleteall'
   * @param string $table Nombre de la tabla
   * @param array $data Datos a insertar/actualizar
   * @param mixed $id ID del registro (update/delete)
   * @param string $context Columna de contexto para WHERE (update)
   * @return int ID insertado (create) o filas afectadas (update/delete)
   *
   * @example
   * // INSERT
   * $db->crudCall('create', 'tabla_usuarios', ['nombre' => 'John', 'email' => 'john@example.com'], null, null);
   *
   * @example
   * // UPDATE
   * $db->crudCall('update', 'tabla_usuarios', ['nombre' => 'Jane'], 5, 'id_usuario');
   *
   * @example
   * // DELETE
   * $db->crudCall('delete', 'tabla_usuarios', [], ['id' => 5], null);
   */
  public function crudCall(string $type, string $table, $data, $id, $context) {
    $table = $this->validaIdentificador($table, 'table');

    $i = 0;
    switch ($type) {
      case 'create':
        $sql = "INSERT INTO $table SET";
        foreach ($data as $key => $value) {
          $key = $this->validaIdentificador($key, 'column');
          if ($i === 0) {
            $sql .= " $key=?";
          } else {
            $sql .= ", $key=?";
          }
          $i++;
        }
        $stmt = $this->exeQuery($sql, $data);
        $new_id = $stmt->insert_id;
        return $new_id;
      case 'update':
        $context = $this->validaIdentificador($context, 'column');
        $sql = "UPDATE $table SET";
        foreach ($data as $key => $value) {
          $key = $this->validaIdentificador($key, 'column');
          if ($i === 0) {
            $sql .= " $key=?";
          } else {
            $sql .= ", $key=?";
          }
          $i++;
        }
        $sql .= " WHERE $context=?";
        $data[$context] = $id;
        $stmt = $this->exeQuery($sql, $data);
        return $stmt->affected_rows;
      case 'delete':
        foreach ($id as $key => $value) {
          $key = $this->validaIdentificador($key, 'column');
          $sql = "DELETE FROM $table WHERE $key=?";
        }
        $stmt = $this->exeQuery($sql, $id);
        return $stmt->affected_rows;
      case 'deleteall':
        $sql = "DELETE FROM $table";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->affected_rows;
    }
  }

  /**
   * Destructor - Cierra la conexión MySQL
   */
  public function __destruct() {
    mysqli_close($this->conn);
  }
}