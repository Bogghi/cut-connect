<?php
namespace App\Utility;

use PDO;

class _DataAccess
{
    protected ?PDO $pdo;

    public function __construct(?PDO $pdo = null) {
        $this->pdo = $pdo;
    }

    private function connectPdo(): void
    {
        if(!$this->pdo) {
            $this->pdo = new PDO('mysql:host=' . DB_HOST . ';port=3306;dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASSWORD);
        }
    }

    private function interpretField($field) {
        if(is_bool($field)) {
            return $field ? 1 : 0;
        }
        return ($field === "null") ? null : $field;
    }

    public function get(
        $table,
        $args,
        $single = false,
        $fields = null,
        $join = null,
        $in = null,
        $between = null,
        $countPartial = false,
        $distinct = false,
        $fetchMode = PDO::FETCH_ASSOC
    ): mixed {
        $this->connectPdo();

        $limitQueryPart = "";
        if (isset($args["page_size"])) {
            $limitQueryPart = " LIMIT " . intval($args["page_size"]);
            if (isset($args["offset"])) {
                $limitQueryPart = " LIMIT " . intval($args["offset"]) . ", " . intval($args["page_size"]);
                unset($args["offset"]);
            }

            unset($args["page_size"]);
        }

        $orderby = "";
        if (isset($args["sort"])) {
            $sortFieldWithoutTable = $args["sort"];
            if (strpos($sortFieldWithoutTable, ".") !== false) {
                $sortFieldWithoutTable = explode(".", $args["sort"])[1];
            }
            $orderby = " ORDER BY " . $args["sort"];

            if (isset($args["sort_dir"])) {
                if ($args["sort_dir"] === "ASC" || $args["sort_dir"] === "DESC") {
                    $orderby .= " " . $args["sort_dir"];
                }
                unset($args["sort_dir"]);
            }
            unset($args["sort"]);
        }

        $wherePart = "";
        $params = [];
        if (!empty($args)) {
            $wherePart = ' WHERE ';
            $i = 0;
            foreach ($args as $keyArg => $value) {
                if ($i !== 0) {
                    $wherePart .= 'AND ';
                }
                if ($value === null) {
                    $wherePart .= $keyArg . ' IS NULL ';
                    $i++;
                    continue;
                }
                $isString = is_string($value);
                $notPart = $isString && strlen($value) > 0 && $value[0] === "!" ? "!" : "";
                $lessPart = $isString && strlen($value) > 1 && $value[0] === "<" ? "<" : "";
                $morePart = $isString && strlen($value) > 1 && $value[0] === ">" ? ">" : "";
                $likePart = $isString && strlen($value) > 1 && $value[0] === "%" && $value[strlen($value) - 1] === "%" ? "LIKE" : "=";
                $wherePart .= $keyArg . ' ' . $notPart . $lessPart . $morePart . $likePart . ' ? ';

                $value = $isString ? ltrim($value, "!><") : $value;
                $params[] = $this->interpretField($value);
                $i++;
            }
        }

        if ($in && count($in) > 0) {
            if (count($params) === 0) {
                $wherePart .= " WHERE ";
            }
            foreach ($in as $param => $values) {
                if (count($params) !== 0) {
                    $wherePart .= 'AND ';
                }
                if (count($values) <= 0) {
                    $wherePart .= ' 0 ';
                    continue;
                }
                $implodedQuestions = rtrim(str_repeat("?,", count($values)), ",");
                $wherePart .= $param . " IN (" . $implodedQuestions . ") ";
                $params = array_merge($params, $this->interpretField($values));
            }
        }

        $fieldsPart = "*";
        if ($fields && count($fields) > 0) {
            $fieldsPart = rtrim(implode(",", $fields), ",");
        }

        $joinPart = "";
        if ($join && count($join) <= 10) {
            foreach($join as $joinTable => $tableId) {
                $joinPart .= " INNER JOIN $joinTable ON $joinTable.$tableId = $table.$tableId ";
            }
        }

        $sql = "SELECT " . ($distinct ? "DISTINCT " : "") . $fieldsPart .
            " FROM " . $table . $joinPart . $wherePart . $orderby . $limitQueryPart;

        $this->connectPdo();
        $stmt = $this->pdo->prepare($sql);
        $exec = $stmt->execute($params);
        $stmt->debugDumpParams();

        if ($exec) {
            $result = $single ? $stmt->fetch($fetchMode) : $stmt->fetchAll($fetchMode);

            if ($countPartial) {
                $stmt = $this->pdo->prepare("SELECT COUNT(*) AS result_num FROM " . $table . $joinPart . $wherePart);
                $stmt->execute($params);
                $resultNum = $stmt->fetch($fetchMode);
                if ($resultNum) {
                    $result = [
                        "selected" => $result,
                        "selected_num" => $resultNum["result_num"]
                    ];
                }
            }
        } else {
            $result = [];
        }

        return $result;
    }
}