<?php

namespace App;

class ModelCustom
{
    public static function hasManyOnManyServers($databaseType, $related, $foreignKey, $localValue)
    {
        $servers = config('dofus.servers');
        $entities = [];

        foreach ($servers as $server) {
            $entitiesList = self::hasManyOnOneServer($databaseType, $server, $related, $foreignKey, $localValue);

            foreach ($entitiesList as $entity) {
                $entities[] = $entity;
            }
        }

        return $entities;
    }

    public static function hasManyOnOneServer($databaseType, $server, $related, $foreignKey, $localValue)
    {
        $entities = [];
        $entitiesList = $related::on($server . '_' . $databaseType)->where($foreignKey, $localValue)->get();

        foreach ($entitiesList as $entity) {
            $entity->server = $server;
            $entities[] = $entity;
        }

        return $entities;
    }

    public static function hasOneOnOneServer($databaseType, $server, $related, $foreignKey, $localValue)
    {
        $entity = $related::on($server . '_' . $databaseType)->where($foreignKey, $localValue)->first();

        if ($entity) {
            $entity->server = $server;
        }

        return $entity;
    }
}
