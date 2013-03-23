<?php
namespace PHPRelease;
use CLIFramework\Application;
use RuntimeException;

class Console extends Application
{
    const NAME = "PHPRelease";
    const VERSION = "0.0.1";

    public $config;

    public function options($opts)
    {
        parent::options($opts);
        $opts->add('dry','dryrun mode.');
        foreach( $this->getTaskObjects() as $task ) {
            $task->options($opts);
        }
    }

    public function getTaskObjects()
    {
        $tasks = array();
        $steps = $this->getSteps();
        foreach( $steps as $step ) {
            if ( file_exists($step) ) {
                continue;
            }

            $task = null;
            if ( class_exists( $step, true ) ) {
                $task = new $step( $this->logger, $this->getConfig() );
            } else {
                // built-in task
                $taskClass = 'PHPRelease\\Tasks\\' . $step;
                if ( class_exists($taskClass, true ) ) {
                    $task = new $taskClass( $this->logger , $this->getConfig() );
                }
            }
            if ( ! $task ) {
                throw new Exception("Task $step not found.");
            }
            $tasks[] = $task;
        }
        return $tasks;
    }

    public function runSteps($steps, $dryrun = false)
    {
        foreach( $steps as $step ) {
            if ( file_exists( $step ) ) {
                if ( ! is_executable($step) ) {
                    throw new RuntimeException("$step is not an executable.");
                }
                $this->logger->info("===> Running $step");
                if ( ! $dryrun ) {
                    $lastline = system($step, $retval);
                    if ( $retval ) {
                        $this->logger->error($lastline);
                        $this->logger->error("===> $step failed, aborting...");
                        exit(0);
                    }
                    continue;
                }
            }

            $task = null;
            if ( class_exists( $step, true ) ) {
                $task = new $step( $this->logger , $this->getConfig(), $this->options );
            } else {
                // built-in task
                $taskClass = 'PHPRelease\\Tasks\\' . $step;
                if ( class_exists($taskClass, true ) ) {
                    $task = new $taskClass( $this->logger , $this->getConfig() , $this->options );
                }
            }
            if ( ! $task ) {
                $this->logger->error("===> Taks $step not found, aborting...");
                exit(0);
            }

            $this->logger->info("===> Running " . get_class($task) );
            if ( ! $dryrun ) {
                $retval = $task->run();
                if ( false === $retval ) {
                    $this->logger->error("===> $task failed, aborting...");
                    exit(0);
                }
            }
        }
    }


    public function getSteps()
    {
        $config = $this->getConfig();
        return preg_split('#\s*,\s*#', $config['Steps'] );
    }

    public function getConfig()
    {
        if ( $this->config ) {
            return $this->config;
        }
        $config = parse_ini_file('phprelease.ini');
        return $this->config = $config;
    }


    public function execute()
    {
        $config = $this->getConfig();
        if ( isset($config['autoload']) ) {
            if ( $a = $config['autoload'] ) {
                $this->logger->info("===> Found autoload script, loading...");
                $loader = require $a;
            }
        }
        elseif ( file_exists('vendor/autoload.php') ) {
            $this->logger->info("===> Found autoload script, loading...");
            $loader = require "vendor/autoload.php";
        }


        $steps = $this->getSteps();
        $this->runSteps($steps, $this->options->dryrun);
    }
}
