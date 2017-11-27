<?php

namespace Superbalist\LaravelPrometheusExporter;

use InvalidArgumentException;
use Predis\Client;
use Prometheus\Storage\Adapter;
use Prometheus\Storage\APC;
use Prometheus\Storage\InMemory;
use Prometheus\Storage\Redis;
use Prometheus\Storage\Predis;

class StorageAdapterFactory
{
    /**
     * Factory a storage adapter.
     *
     * @param string $driver
     * @param array $config
     *
     * @return Adapter
     */
    public function make($driver, array $config = [])
    {
        switch ($driver) {
            case 'memory':
                return new InMemory();
            case 'redis':
                return $this->makeRedisAdapter($config);
            case 'predis':
                return $this->makePredisAdapter($config);
            case 'apc':
                return new APC();
        }

        throw new InvalidArgumentException(sprintf('The driver [%s] is not supported.', $driver));
    }

    /**
     * Factory a redis storage adapter.
     *
     * @param array $config
     *
     * @return Redis
     */
    protected function makeRedisAdapter(array $config)
    {
        if (isset($config['prefix'])) {
            Redis::setPrefix($config['prefix']);
        }
        return new Redis($config);
    }

    protected function makePredisAdapter(array $config)
    {
        if (isset($config['prefix'])) {
            Predis::setPrefix($config['prefix']);
            unset($config['prefix']);
        }
        return new Predis(new Client($config));
    }
}
