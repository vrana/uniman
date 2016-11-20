<?php

namespace Adminerng\Drivers\Elasticsearch\Forms;

use Adminerng\Core\CredentialsFormInterface;
use Nette\Application\UI\Form;

class ElasticsearchCredentialsForm implements CredentialsFormInterface
{
    public function addFieldsToForm(Form $form)
    {
        $form->addTextArea('hosts', 'elasticsearch.credentials_form.hosts.label')
            ->setAttribute('placeholder', 'localhost:9200')
            ->setOption('description', 'elasticsearch.credentials_form.hosts.description');
    }
}
