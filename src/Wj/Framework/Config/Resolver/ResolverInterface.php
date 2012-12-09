<?php

namespace Wj\Framework\Config\Resolver;


interface ResolverInterface
{
    /**
     * Resolves a input to something readable for a machine.
     *
     * @param mixed $input
     *
     * @return mixed
     */
    public function resolve($input);
}
