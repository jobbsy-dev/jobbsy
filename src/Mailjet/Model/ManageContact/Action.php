<?php

namespace App\Mailjet\Model\ManageContact;

enum Action: string
{
    case ADD_FORCE = 'addforce';

    case ADD_NO_FORCE = 'addnoforce';

    case REMOVE = 'remove';

    case UNSUB = 'unsub';
}
