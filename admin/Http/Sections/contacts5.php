<?php
namespace Admin\Http\Sections;
use AdminColumn;
use AdminDisplay;
use AdminDisplayFilter;
use AdminForm;
use AdminFormElement;
use FormElements;
use AdminSection;
use App\Model\Company;
use App\Model\Country;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Section;
/**
 * Class Contacts5
 *
 * @property \App\Model\Contact5 $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class Contacts5 extends Section
{
    /**
     * @see http://sleepingowladmin.ru/docs/model_configuration#ограничение-прав-доступа
     *
     * @var bool
     */
    protected $checkAccess = false;
    /**
     * @var string
     */
    protected $title = 'Contacts Tabbed Forms';
    /**
     * @var string
     */
    protected $alias;
    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        $display = AdminDisplay::table();
        $display->with('country', 'companies');
        $display->setFilters(
            AdminDisplayFilter::related('country_id')->setModel(Country::class),
            AdminDisplayFilter::field('country.title')->setOperator(\SleepingOwl\Admin\Display\Filter\FilterBase::CONTAINS)
        );
        $display->setColumns([
            AdminColumn::image('photo', 'Photo')->setWidth('100px'),
            AdminColumn::link('fullName', 'Name')->setWidth('200px'),
            AdminColumn::datetime('birthday', 'Birthday')->setFormat('d.m.Y')->setWidth('150px')->setHtmlAttribute('class', 'text-center'),
            AdminColumn::text('country.title', 'Country')->append(AdminColumn::filter('country_id')),
            AdminColumn::lists('companies.title', 'Companies'),
        ]);
        return $display;
    }
    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        $formPrimary = AdminForm::form()->addElement(
               AdminFormElement::columns()
                ->addColumn([
                    AdminFormElement::text('firstName', 'First Name')->required()
                ], 3)
                ->addColumn([
                    AdminFormElement::text('lastName', 'Last Name')->required()->addValidationMessage('required', 'You need to set last name')
                ], 3)
                ->addColumn([
                    AdminFormElement::date('birthday', 'Birthday')->setFormat('d.m.Y')->required()
                ])
                 ->addColumn([
                        AdminFormElement::select('country_id', 'Country', Country::class)->setDisplay('title')
                ], 4)
        );   
        $formVisual = AdminForm::form()->addElement(
            new \SleepingOwl\Admin\Form\FormElements([
                AdminFormElement::textarea('address', 'Address')->required('so sad but this field is empty')
            ])
        );     
        $formVisual = AdminForm::form()->addElement(
            new \SleepingOwl\Admin\Form\FormElements([
                AdminFormElement::wysiwyg('address', 'Address')->required('so sad but this field is empty.')
            ])
        );     
             
             
        $tabs = AdminDisplay::tabbed();

        $tabs->appendTab($formPrimary,  'Primary');
     
        $tabs->appendTab($formHTML,     'HTML Adress Redactor');

        $tabs->appendTab($formVisual,   'Visual Adress Redactor');
             
             
        return $tabs;
    }
    /**
     * @return FormInterface
     */
    public function onCreate()
    {
        return $this->onEdit(null);
    }
}
