<?php

class Setting
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /*
    |--------------------------------------------------------------------------
    | GET ONE SETTING
    |--------------------------------------------------------------------------
    */

    public function get(
        int $businessId,
        string $key,
        $default = null
    ) {
        $stmt = $this->db->prepare(
            "
            SELECT setting_value
            FROM system_settings
            WHERE business_id = :business_id
              AND setting_key = :setting_key
            LIMIT 1
            "
        );

        $stmt->execute([
            ':business_id' => $businessId,
            ':setting_key' => $key
        ]);

        $value = $stmt->fetchColumn();

        if ($value === false) {
            return $default;
        }

        return $value;
    }


    /*
    |--------------------------------------------------------------------------
    | GET ALL SETTINGS
    |--------------------------------------------------------------------------
    */

    public function all(
        int $businessId
    ): array {

        $stmt = $this->db->prepare(
            "
            SELECT
                setting_key,
                setting_value
            FROM system_settings
            WHERE business_id = :business_id
            ORDER BY setting_key ASC
            "
        );

        $stmt->execute([
            ':business_id' => $businessId
        ]);

        $rows = $stmt->fetchAll(
            PDO::FETCH_ASSOC
        );

        $settings = [];

        foreach ($rows as $row) {

            $settings[
                $row['setting_key']
            ] = $row['setting_value'];
        }

        return $settings;
    }


    /*
    |--------------------------------------------------------------------------
    | SET SETTING
    |--------------------------------------------------------------------------
    */

    public function set(
        int $businessId,
        string $key,
        $value
    ): bool {

        $stmt = $this->db->prepare(
            "
            INSERT INTO system_settings
            (
                business_id,
                setting_key,
                setting_value
            )
            VALUES
            (
                :business_id,
                :setting_key,
                :setting_value
            )

            ON DUPLICATE KEY UPDATE

                setting_value =
                    VALUES(setting_value),

                updated_at =
                    CURRENT_TIMESTAMP
            "
        );

        return $stmt->execute([
            ':business_id' => $businessId,
            ':setting_key' => $key,
            ':setting_value' => $value
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | SET MANY
    |--------------------------------------------------------------------------
    */

    public function setMany(
        int $businessId,
        array $settings
    ): bool {

        try {

            $this->db->beginTransaction();

            foreach ($settings as $key => $value) {

                $this->set(
                    $businessId,
                    $key,
                    $value
                );
            }

            $this->db->commit();

            return true;

        } catch (Throwable $e) {

            if (
                $this->db->inTransaction()
            ) {
                $this->db->rollBack();
            }

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE SETTING
    |--------------------------------------------------------------------------
    */

    public function delete(
        int $businessId,
        string $key
    ): bool {

        $stmt = $this->db->prepare(
            "
            DELETE FROM system_settings
            WHERE business_id = :business_id
              AND setting_key = :setting_key
            "
        );

        return $stmt->execute([
            ':business_id' => $businessId,
            ':setting_key' => $key
        ]);
    }
}