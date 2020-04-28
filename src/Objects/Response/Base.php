<?php
/**
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Igor Borodikhin <iborodikhin@gmail.com>
 * @license MIT
 */
namespace Wooppay\Objects\Response;

use Wooppay\Objects\Exceptions\BadCredentials;
use Wooppay\Objects\Exceptions\LowBalance;
use Wooppay\Objects\Exceptions\UnsuccessfulResponse;
use Wooppay\Objects\Interfaces\Response;

/**
 * Base response object.
 */
abstract class Base implements Response
{
    /**
     * Result code for success.
     */
    const ERROR_CODE_NONE = 0;

    /**
     * Result code for authentication error.
     */
    const ERROR_CODE_BAD_CREDENTIALS = 5;

    /**
     * Result code for low balance.
     */
    const ERROR_CODE_LOW_BALANCE = 226;

    /**
     * Checks response for errors.
     *
     * @param  \stdClass                                        $response
     * @return boolean
     * @throws \Wooppay\Objects\Exceptions\BadCredentials
     * @throws \Wooppay\Objects\Exceptions\LowBalance
     * @throws \Wooppay\Objects\Exceptions\UnsuccessfulResponse
     */
    public static function checkResponse(\stdClass $response)
    {
        switch (true) {
            case isset($response->error_code) && $response->error_code == self::ERROR_CODE_NONE:
                return true;
                break;

            case isset($response->error_code) && $response->error_code == self::ERROR_CODE_BAD_CREDENTIALS:
                throw new BadCredentials();
                break;

            case isset($response->error_code) && $response->error_code == self::ERROR_CODE_LOW_BALANCE:
                throw new LowBalance();
                break;

            default:
                throw new UnsuccessfulResponse(
                    $response,
                    isset($response->error_code) ? $response->error_code : 500
                );

                break;
        }
    }
}
