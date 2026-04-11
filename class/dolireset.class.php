<?php

/**
 *                 This modul can delete all generated documents in Dolibarr
 *                 documents folder and delete all data (reset) from the database
 *
 * @date           File created on Thu Aug 26 2021 20:17:18
 *
 * @category       Dolibarr plugin
 * @package        Doli Reset
 * @link           https://blacktiehost.com/shop/dolibarr-modules/
 * @since          1.0
 * @version        1.0
 * @author         Milen Karaganski <milen@blacktiehost.com>
 * @license        GPL-2.0+
 * @license        http://www.gnu.org/licenses/gpl-2.0.txt
 * @copyright      Copyright (c) 2021 Rapid Progress Ltd.
 *
 */

class DoliReset
{
    /**
     * @var DoliDB Database handler.
     */
    public $db;

    /**
     * @var string Error code (or message)
     */
    public $error = '';

    /**
     * @var array Errors
     */
    public $errors = array();

    /**
     * @var array Hook results. Propagated to $hookmanager->resArray for later reuse
     */
    public $results = array();

    /**
     * @var string String displayed by executeHook() immediately after return
     */
    public $resprints;

    /**
     * Constructor
     *
     *  @param    DoliDB    $db    Database handler
     */
    public function __construct($db)
    {
        $this->db = $db;
    }

    /**
     * Delete the content of Dolibarr documents folder
     *
     * @param string $rootDir Path to dolibarr documents folder
     * @return int
     */
    public function cleanDocumentsFolder($rootDir)
    {
        global $langs;

        $error = '';

        $items = glob(rtrim($rootDir, '/') . '/*');

        if (!is_array($items)) {
            return -1;
        }

        foreach ($items as $item) {
            if (is_dir($item)) {
                // Level 1 (module folder like facture/)
                $this->deleteInside($item);
            }
        }

        return 1;
    }

    private function deleteInside($dir)
    {
        $items = glob(rtrim($dir, '/') . '/*');

        if (!is_array($items)) {
            return;
        }

        foreach ($items as $item) {
            if (is_dir($item)) {
                $this->deleteDir($item); // delete completely
            } elseif (is_file($item)) {
                unlink($item);
            }
        }
    }

    private function deleteDir($dir)
    {
        $items = glob(rtrim($dir, '/') . '/*');

        if (is_array($items)) {
            foreach ($items as $item) {
                if (is_dir($item)) {
                    $this->deleteDir($item);
                } else {
                    unlink($item);
                }
            }
        }

        rmdir($dir);
    }

    /**
     * Drop all tables in databased function
     *
     * @return int
     */
    public function deleteDatabase()
    {
        global $dolibarr_main_db_prefix;

        if (empty($dolibarr_main_db_prefix)) {
            return -1;
        }

        $this->db->query('SET foreign_key_checks = 0');

        $sql = "SHOW TABLES LIKE '" . $this->db->escape($dolibarr_main_db_prefix) . "%'";
        $result = $this->db->query($sql);

        if ($result) {
            while ($row = $this->db->fetch_array($result, MYSQLI_NUM)) {
                $table = array_values($row)[0];

                if (
                    empty($table) ||
                    empty($dolibarr_main_db_prefix) ||
                    !str_starts_with($table, $dolibarr_main_db_prefix)
                ) {
                    continue;
                }

                $this->db->DDLDropTable($table);
            }
        } else {
            $this->error = $this->db->lasterror();
            $this->errors[] = $this->db->lasterror();
            return -1;
        }

        $this->db->query('SET foreign_key_checks = 1');

        return 1;
    }
}
