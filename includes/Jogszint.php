<?php

namespace Infoinventory\includes;

enum Jogszint : int {
    case mindenki = 0;
    case vendeg = 1;
    case tagok = 2;
    case adminok = 3;
    case foadminok = 4;
}