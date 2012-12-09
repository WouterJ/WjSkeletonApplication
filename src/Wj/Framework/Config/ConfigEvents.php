<?php

namespace Wj\Framework\Config;

final class ConfigEvents
{
    /**
     * The READ event occurs before parsing the data.
     *
     * This event allows you to manipulate the configuration file, e.g. 
     * parsing logical names.
     *
     * @var string
     */

    const READ = 'wj_config.read';
    /**
     * The PARSE event occurs when the config file is parsed, just before
     * returning it.
     *
     * This event allows you to manipulate the data, for instance add some
     * default configuration keys. The event listener method receives an 
     * assocasive array that is returned by the parser.
     *
     * @var string
     */
    const PARSE = 'wj_config.parse';
}
