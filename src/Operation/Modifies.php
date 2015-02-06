<?php

namespace gamringer\JSONPatch\Operation;

interface Modifies
{
	public function apply();

	public function getTarget();

	public function setTarget($target);
}