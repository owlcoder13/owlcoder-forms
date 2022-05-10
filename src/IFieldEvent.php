<?php

namespace Owlcoder\Forms;

interface IFieldEvent
{
    const EVENT_BEFORE_APPLY = 'EVENT_BEFORE_APPLY';
    const EVENT_BEFORE_SET = 'EVENT_BEFORE_SET';
    const EVENT_AFTER_SAVE = 'EVENT_AFTER_SAVE';
    const EVENT_FETCH_VALUE = 'EVENT_FETCH_VALUE';
}
