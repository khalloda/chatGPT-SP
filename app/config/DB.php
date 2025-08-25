<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

/**
 * Purpose: PDO singleton with hardened options & generic public error messaging.
 * Inputs: Env DB_* vars
 * Outputs: PDO instance; helpers for tx and queries
 */
final class DB
{
    private static ?PDO $pdo = null;

    public static function pdo(): PDO
    {
        if (self::$pdo) return self::$pdo;

        $host = Env::get('DB_HOST', '127.0.0.1');
        $port = (int) Env::get('DB_PORT', 3306);
        $name = Env::get('DB_NAME', 'sp_spareparts');
        $user = Env::get('DB_USER', 'root');
        $pass = Env::get('DB_PASS', '');
        $charset = Env::get('DB_CHARSET', 'utf8mb4');

        $dsn = "mysql:host={$host};port={$port};dbname={$name};charset={$charset}";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            self::$pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            error_log('DB connection failed: ' . $e->getMessage()); // detailed only in logs
            throw new PDOException('Database connection failed.');  // generic message to users
        }
        return self::$pdo;
    }

    public static function query(string $sql, array $params=[]): \PDOStatement
    {
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            error_log('DB query failed: ' . $e->getMessage());
            throw new PDOException('Database query failed.');
        }
    }

    public static function begin(): bool { return self::pdo()->beginTransaction(); }
    public static function commit(): bool { return self::pdo()->commit(); }
    public static function rollBack(): bool { return self::pdo()->rollBack(); }
    public static function lastId(): string { return self::pdo()->lastInsertId(); }
}

