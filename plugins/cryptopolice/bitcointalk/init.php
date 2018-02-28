<?php

Event::listen('bitcointalk.crawlEnd', 'CryptoPolice\Bitcointalk\Classes\EventListeners\BtcAccountVerification');