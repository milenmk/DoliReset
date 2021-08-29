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

/* Copyright (C) 2001-2005 Rodolphe Quiedeville <rodolphe@quiedeville.org>
 * Copyright (C) 2004-2015 Laurent Destailleur  <eldy@users.sourceforge.net>
 * Copyright (C) 2005-2012 Regis Houssin        <regis.houssin@inodbox.com>
 * Copyright (C) 2015      Jean-François Ferry	<jfefe@aternatik.fr>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file       dolireset/doliresetindex.php
 * \ingroup    dolireset
 * \brief      Home page of dolireset top menu
 */

// Load Dolibarr environment
$res = 0;
// Try main.inc.php into web root known defined into CONTEXT_DOCUMENT_ROOT (not always defined)
if (!$res && !empty($_SERVER["CONTEXT_DOCUMENT_ROOT"])) {
    $res = @include $_SERVER["CONTEXT_DOCUMENT_ROOT"] . "/main.inc.php";
}
// Try main.inc.php into web root detected using web root calculated from SCRIPT_FILENAME
$tmp = empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];
$tmp2 = realpath(__FILE__);
$i = strlen($tmp) - 1;
$j = strlen($tmp2) - 1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i] == $tmp2[$j]) {
    $i--;
    $j--;
}
if (!$res && $i > 0 && file_exists(substr($tmp, 0, ($i + 1)) . "/main.inc.php")) {
    $res = @include substr($tmp, 0, ($i + 1)) . "/main.inc.php";
}
if (!$res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php")) {
    $res = @include dirname(substr($tmp, 0, ($i + 1))) . "/main.inc.php";
}
// Try main.inc.php using relative path
if (!$res && file_exists("../main.inc.php")) {
    $res = @include "../main.inc.php";
}
if (!$res && file_exists("../../main.inc.php")) {
    $res = @include "../../main.inc.php";
}
if (!$res && file_exists("../../../main.inc.php")) {
    $res = @include "../../../main.inc.php";
}
if (!$res) {
    die("Include of main fails");
}

require_once DOL_DOCUMENT_ROOT . '/core/class/html.formfile.class.php';

require_once './class/dolireset.class.php';

// Load translation files required by the page
$langs->loadLangs(array("dolireset@dolireset"));

$action = GETPOST('action', 'aZ09');

// Security check
// if (! $user->rights->dolireset->myobject->read) {
// 	accessforbidden();
// }

$socid = GETPOST('socid', 'int');
if (isset($user->socid) && $user->socid > 0) {
    $action = '';
    $socid = $user->socid;
}
if (!$user->admin) {
    accessforbidden();
}

$max = 5;
$now = dol_now();


/*
 * Actions
 */
//None


/*
 * View
 */

$form = new Form($db);
$formfile = new FormFile($db);
$dolireset = new DoliReset($db);

llxHeader("", $langs->trans("ModuleDoliResetName"));

print load_fiche_titre($langs->trans("ModuleDoliResetName"), '', 'dolireset.png@dolireset');

if ($action == 'deleteFF') {
    $dolireset->deleteContentDocumentsFolder($dolibarr_main_data_root);
    print setEventMessages($langs->trans("FFDeleteConfirm"), null, 'mesgs');
}
if ($action == 'deleteDB') {
    $dolireset->deleteDatabase($dolibarr_main_data_root);
    print '<meta http-equiv="refresh" content="0;url=' . DOL_URL_ROOT . '/install/index.php">';
}

?>

<div class="fichecenter">

    <div class="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        <?php print $langs->trans('ResetWarning'); ?>
    </div>

    <div class="fichethirdleft">
        <div class='div-table-responsive'>
            <table width='100%' class='tagtable liste'>
                <thead>
                    <tr>
                        <td colspan="2">
                            <?php print $langs->trans('DeleteFF'); ?>
                        </td>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php print $langs->trans('Location'); ?>: <?php print $dolibarr_main_data_root; ?></td>
                        <td><a class="butAction" href="<?php print $_SERVER['PHP_SELF']; ?>?action=deleteFF"><?php print $langs->trans('Delete'); ?></a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="fichetwothirdright">
        <div class="ficheaddleft">
            <div class='div-table-responsive'>
                <table width='100%' class='tagtable liste'>
                    <thead>
                        <tr>
                            <td colspan="2">
                                <?php print $langs->trans('DeleteDB'); ?>
                            </td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php print $langs->trans('Name'); ?>: <?php print $dolibarr_main_db_name; ?><br><?php print $langs->trans('DeleteDBTooltip'); ?></td>
                            <td><a class="butAction" href="<?php print $_SERVER['PHP_SELF']; ?>?action=deleteDB" onclick="return confirm('<?php print $langs->trans('DeleteWarning'); ?>');"><?php print $langs->trans('Delete'); ?></a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?php
// End of page
llxFooter();
$db->close();
