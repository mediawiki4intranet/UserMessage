<?php

/**
 * MediaWiki UserMessage extension
 * Copyright © 2009-2010 Vitaliy Filippov
 * http://yourcmc.ru/wiki/UserMessage_(MediaWiki)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 59 Temple Place - Suite 330, Boston, MA 02111-1307, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

/*

This extension allows to customize messages used by wfMsg(...),
editable through generic article edit interface (MediaWiki:...),
to be customized on per-user basis.

It is useful, for example, for (Bug 61726) personalization of
MediaWiki:Edittools.

USAGE (LocalSettings.php):

require_once("extensions/UserMessage/UserMessage.php");
$wgUserMessageAllowCustomization = array(
    'edittools' => true,
    // 'message_key' => true for each message that you want to allow to be customized
);

*/

$wgExtensionCredits['other'][] = array(
    'name'         => 'User Message',
    'version'      => '2010-03-25',
    'author'       => 'Vitaliy Filippov',
    'url'          => 'http://yourcmc.ru/wiki/index.php/UserMessage_(MediaWiki)',
    'description'  => 'Allows customization of MediaWiki:xxx messages on a per-user basis',
);
$wgHooks['NormalizeMessageKey'][] = 'efUserMessageNormalizeMessageKey';
if (is_null($wgUserMessageAllowCustomization))
    $wgUserMessageAllowCustomization = array('edittools' => true);

function efUserMessageNormalizeMessageKey(&$key, &$useDB, &$langCode, &$transform)
{
    global $wgUserMessageAllowCustomization, $wgUser, $wgMessageCache;
    if ($wgUserMessageAllowCustomization[$key] && $wgUser && $wgUser->getID() &&
        is_object($wgMessageCache))
    {
        $newkey = $key.'/'.$wgUser->getName();
        if (!wfEmptyMsg($newkey, $wgMessageCache->get($newkey, true, $langCode)))
            $key = $newkey;
    }
    elseif (($p = strrpos($key, '/')) !== false &&
        $wgUserMessageAllowCustomization[substr($key, 0, $p)] &&
        User::newFromName(substr($key, $p+1)))
    {
        $key = substr($key, 0, $p);
        $useDB = true;
    }
    return true;
}
