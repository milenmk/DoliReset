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
     * @param    string    $dir    Path to dolibarr documents folder
     * @param    boolean   $remove    If false, then remove the subfolders and files. If true - delete entire folder.
     * @return void
     */
    public function deleteContentDocumentsFolder($dir, $remove = false)
    {
        global $langs;

        $error = '';

        $structure = glob(rtrim($dir, "/") . '/*');
        if (is_array($structure)) {
            foreach ($structure as $file) {
                if (is_dir($file)) {
                    $this->deleteContentDocumentsFolder($file, true);
                } elseif (is_file($file)) {
                    unlink($file);
                }
            }
        }
        if ($remove) {
            rmdir($dir);
        }
        if (!$error) {
            return setEventMessages($langs->trans("FFDeleteConfirm"), null, 'mesgs');
        } else {
            return $error;
        }
    }

    public function deleteDatabase()
    {

        $error = '';

        $this->db->query('SET foreign_key_checks = 0');

        $result = $this->db->query("SHOW TABLES");

        if ($result) {
            while ($row = $this->db->fetch_array($result, MYSQLI_NUM)) {
                $this->db->query('DROP TABLE IF EXISTS ' . $row[0]);
            }
        } else {
            $this->error = $this->db->lasterror();
            $this->errors[] = $this->db->lasterror();
            return -1;
        }

        $this->db->query('SET foreign_key_checks = 1');
    }
}
