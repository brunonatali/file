<?php declare(strict_types=1);

namespace BrunoNatali\Tools\File;

interface OnFileChangeInterface
{
    /**
     * @var int Throwable code for file name not provided 
    */
    const ERROR_FILE_NAME_ABSENT = 0x110;

    /**
     * @var int Throwable code for no callable function provided
    */
    const ERROR_FILE_CALL_ABSENT = 0x111;
    
    /**
     * @var int Throwable code for unknown LoopInterface
    */
    const ERROR_FILE_LOOP_ABSENT = 0x112;
    
    /**
     * @var int Throwable code for a non existent file
    */
    const ERROR_FILE_NOT_EXIST = 0x113;

    /**
     * @var int Stores system method as using inotify extension
    */
    const USE_SYSTEM_INOTIFY = 0x50;
    
    /**
     * @var int Stores system method as periodic check (polling)
    */
    const USE_SYSTEM_POLLING = 0x51;

    /**
     * Just start monitoring system
     * 
     * @return bool
    */
    public function start(): bool;
    
    /**
     * Stop current file monitoring
     * 
     * @return bool
    */
    public function stop(): bool;

    /**
     * Configure / reconfigure polling time
     * 
     * @param float $time New polling time
     * @return bool
    */
    public function setPollingTime(int $time): bool;
}