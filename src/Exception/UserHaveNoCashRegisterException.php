<?php

declare(strict_types=1);

namespace App\Exception;

use InvalidArgumentException as BaseInvalidArgumentException;

class UserHaveNoCashRegisterException extends BaseInvalidArgumentException implements AppException, PublicException
{
}
