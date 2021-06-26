<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class InstallmentType extends Enum
{
    const Main = 0;
    const VAT = 1;
    const Delivery = 2;
}
