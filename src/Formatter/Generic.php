<?php
/**
 * @author Bikash Poudel <bikash.poudel.com@gmail.com>
 */

namespace GrassRoots\ErrorHandler\Formatter;

use Zend\Log\Formatter\FormatterInterface;

/**
 * Class Generic
 *
 * This class can be used to format the errors/exceptions which are written to stream and mail
 * @package GrassRoots\ErrorHandler\Formatter
 */
class Generic implements FormatterInterface
{

    /**
     * @param array $event
     * @return string
     */
    public function format($event)
    {
        $extraParams   = $event['extra'];
        $datetime      = $event['timestamp'];
        $priority      = $event['priorityName'];

        $exceptionBody = '<pre>';
        $exceptionBody .= 'An error has occurred, the system has captured following information:' . PHP_EOL . PHP_EOL;
        $exceptionBody .= 'Date: ' . $datetime->format('d/m/Y H:i:s') . PHP_EOL;
        $exceptionBody .= 'Priority: ' . $priority . PHP_EOL;
        $exceptionBody .= $extraParams['trace'] . PHP_EOL;
        $exceptionBody .= '$_GET:' . PHP_EOL . print_r($_GET, true);
        $exceptionBody .= '$_POST:' . PHP_EOL . print_r($_POST, true);
        $exceptionBody .= '$_SERVER:' . PHP_EOL . print_r($_SERVER, true);
        $exceptionBody .= '$_SESSION:' . PHP_EOL . print_r($_SESSION, true);
        $exceptionBody .= 'Memory Used:' . PHP_EOL . memory_get_usage(true);
        $exceptionBody .= 'Peak memory used:' . PHP_EOL . memory_get_peak_usage(true);
        $exceptionBody .= '</pre>';

        //For generic formatter we don't care about anything other than the exception body
        return $exceptionBody;
    }

    public function getDateTimeFormat()
    {
        // TODO: Implement getDateTimeFormat() method.
    }

    public function setDateTimeFormat($dateTimeFormat)
    {
        // TODO: Implement setDateTimeFormat() method.
    }
}
