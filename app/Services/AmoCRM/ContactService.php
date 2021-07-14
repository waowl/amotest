<?php


namespace App\Services\AmoCRM;


use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Models\ContactModel;
use AmoCRM\Models\CustomFieldsValues\MultitextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\MultitextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\MultitextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use Illuminate\Http\Request;

class ContactService
{
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

        $apiClient =  $this->authService->initApiClient();

        //Создадим контакт
        $contact = new ContactModel();
        $contact->setName($request->input('name'));

        //Получим коллекцию значений полей контакта
        $customFields = $contact->getCustomFieldsValues();

        if (! $customFields) {
            $customFields = new CustomFieldsValuesCollection();
        }
        //Получим значение поля по его коду
        $phoneField = $customFields->getBy('fieldCode', 'PHONE');


        if (empty($phoneField)) {
            $phoneField = (new MultitextCustomFieldValuesModel())->setFieldCode('PHONE');
            $customFields->add($phoneField);
        }

        $emailField = $customFields->getBy('fieldCode', 'EMAIL');
        //Если значения нет, то создадим новый объект поля и добавим его в коллекцию значений
        if (empty($emailField)) {
            $emailField = (new MultitextCustomFieldValuesModel())->setFieldCode('EMAIL');
            $customFields->add($emailField);
        }

        //Установим значение поля
        $phoneField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setValue($request->input('phone'))
                )
        );

        //Установим значение поля
        $emailField->setValues(
            (new MultitextCustomFieldValueCollection())
                ->add(
                    (new MultitextCustomFieldValueModel())
                        ->setValue($request->input('email'))
                )
        );

        $contact->setCustomFieldsValues($customFields);


        $leadsService  = $apiClient->leads();

        $lead = new LeadModel();
        $lead->setName('Сделка')
            ->setContacts(
                (new ContactsCollection())
                    ->add($contact));

        try {
            $apiClient->contacts()->addOne($contact);
            $apiClient->leads()->addOne($lead);
        } catch (AmoCRMApiException $e) {
            return false;
        }

        return true;
    }
}
