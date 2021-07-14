<?php


namespace App\Services\AmoCRM;


use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFields\CustomFieldModel;
use AmoCRM\Models\CustomFields\TextCustomFieldModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Illuminate\Http\Request;

class ContactService
{
    const SOURCE = 'site';

    /**
     * @var AuthService
     */
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function create(Request $request): bool
    {

        $apiClient = $this->authService->initApiClient();

        //Создадим контакт
        $contact = new ContactModel();
        $contact->setName($request->input('name'));

        //Получим коллекцию значений полей контакта
        $customFields = $contact->getCustomFieldsValues();

        if (!$customFields) {
            $customFields = new CustomFieldsValuesCollection();


            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
            $customFields->add($phoneField);


            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $customFields->add($emailField);

        //Установим значение поля
            $phoneField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setValue($request->input('phone'))
                )
            );

            $emailField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setValue($request->input('email'))
                )
        );

            $contact->setCustomFieldsValues($customFields);
    }

        $lead = new LeadModel();
        $lead->setName('Сделка')
            ->setContacts(
                (new ContactsCollection())
                    ->add($contact));


        try {
            $apiClient->contacts()->addOne($contact);
            $apiClient->leads()->addOne($lead);
        } catch (AmoCRMApiException $e) {
                dd($e);
        }

        return true;
    }
}
