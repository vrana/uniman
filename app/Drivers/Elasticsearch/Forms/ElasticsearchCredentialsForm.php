<?php

namespace Adminerng\Drivers\Elasticsearch\Forms;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class ElasticsearchCredentialsForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addText('host', 'elasticsearch.credentials_form.host.label')
            ->setAttribute('placeholder', 'localhost:9200');
        $form->addText('database', 'elasticsearch.credentials_form.index.label');
    }
}
