<?php

namespace Owlcoder\Forms;

interface IFormEvent
{
    const FIELD_INSTANCE_GET_VALUE = 'onFieldInstanceGetValue';
    const FIELD_INSTANCE_SET_VALUE = 'onFieldInstanceSetValue';

    const BEFORE_SAVE = 'onBeforeSave';
    const AFTER_SAVE = 'onAfterSave';

    const BEFORE_VALIDATE = 'onBeforeValidate';
    const AFTER_VALIDATE = 'onAfterValidate';
}
