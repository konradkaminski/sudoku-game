<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once 'Zend/Auth/Adapter/Interface.php';

/**
 * @see Zend_Db_Adapter_Abstract
 */
require_once 'Zend/Db/Adapter/Abstract.php';

/**
 * @see Zend_Auth_Result
 */
require_once 'Zend/Auth/Result.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage Adapter
 * @copyright  Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class My_Auth_Adapter_Guest implements Zend_Auth_Adapter_Interface
{


    public function authenticate() {
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, array());
    }

}
