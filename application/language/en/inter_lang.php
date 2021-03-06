<?php
$localization = array(
/*
|--------------------------------------------------------------------------
| ADMIN interface language
|--------------------------------------------------------------------------
*/
    'yes' => 'Yes',
    'no' => 'No',

    'sign_in_label' => 'Login',
    'sign_in' => 'Sign in',
    'login' => 'Login',
    'password' => 'Password',

    'save' => 'Save',
    'cancel' => 'Cancel',
    'saved' => 'Successfully saved',
    'error' => 'An unknown error occurred',
    'error_code' => array(
        401 => 'Access allowed only for registered users.',
        403 => 'Forbidden',
        404 => 'Not Found',
        500 => 'Internal Server Error'
    ),
    'removed' => 'Successfully removed',

    'main_page' => 'To main page',
    'loged_as' => 'You are logged in as',
    'log_out' => 'Logout',

    'on_page' => 'On page',
    'position' => 'Position',
    'name' => 'Name',
    'action' => 'Action',


    'menu' => array(
        'add' => 'Adding menu',
        'upd' => 'Editing menu',
        'remove' => 'Delete menu',
        'conf_delete' => 'Do you really want to delete the menu',
        'info' => 'Information about menu',
        'role' => 'Role',
        'roles' => array('header', 'footer', 'any')
    ),

    'page' => array(
        'add' => 'Add new page/component',
        'upd' => 'Update page/component',
        'remove' => 'Remove page/component',
        'name' => 'Page/Component name',
        'type' => 'Type',
        'rel_menu_item' => 'Related menu item',
        'relate' => 'Relate ',
        'with_m' => ' with',
        'menu_item' => 'Menu item',
        'type_menu_item' => 'Type menu item',
        'connect_m_i' => 'Relate menu item',
        'dis_connect_m_i' => 'Remove relation with menu item',
        'dis_connected_m_i' => 'Removed relation with menu item',
        'component_exist' => 'The component you are trying to add already exists!'
    ),

    'comp_type' => array(
        'add' => 'Add the component type',
        'upd' => 'Update the component type',
        'conf_remove' => 'Do you really want to delete the component type',
        'name' => 'Component type name',
        'clear_name' => 'Component type clear name',
        'system' => 'System component type',
        'directory' => 'Directory',
        'unique' => 'Unique',
        'main_info' => 'Main info',
        'info' => 'Component type information',
        'functions' => 'Functions',
        'library' => 'Library',
        'admin_jmvc' => 'Admin JMVC directory',
        'user_jmvc' => 'User JMVC directory',
        'server_controller' => 'Server controller function',
        'button_panel' => 'Has button',
        'settings' => 'Settings',
        'minimise' => 'Minimise',
        'maximise' => 'Maximise',
        'add_function' => 'Add function',
        'comp_functions' => 'Component type functions',
        'empty_functions' => 'There are no functions in this component type.',
        'remove_func_error' => 'Failure to remove the function.',

        'function' => 'Function',
        'function_clear' => 'Clear function',
        'function_save' => 'Save function',
        'function_remove' => 'Remove function',
    ),

    'integrator' => array(
        'integrators' => 'Integrators',
        'add' => 'Create new integrator',
        'upd' => 'Update integrator',
        'remove' => 'Remove integrator',
        'conf_remove' => 'Do you really want to delete this integrator?',
        'name' => 'Integrator\'s name',
        'clear_name' => 'Clear integrator\'s name',
        'directory' => 'Directory',
        'unique' => 'Unique',
        'main_info' => 'Main info',
        'info' => 'Integrator information',
        'library' => 'Library',
        'action' => 'Action',

        'placeholders' => 'Placeholders',
        'miniBlocks' => 'Mini blocks',
        'productBlocks' => 'Product blocks',
        'placeholder' => 'Placeholder',
        'placeholderName' => 'Placeholder name',
        'addPlaceholder' => 'Add placeholder',
        'savePlaceholder' => 'Save placeholder',
        'savedPlaceholder' => 'Placeholder saved',
        'removePlaceholder' => 'Delete placeholder',
        'removeConfirm' => 'Do you really want to delete this placeholder?',
        'integratorPlaceholders' => 'Integrator placeholders',
        'noPlaceholders' => 'There are no placeholders related to this integrator.',
        'removePlaceholderError' => 'Failed to remove placeholder.',
    ),

    'placeholder' => array(
        'add' => 'Create new placeholder',
        'upd' => 'Update placeholder',
        'set' => 'Set placeholder',
        'remove' => 'Remove placeholder',
        'conf_remove' => 'Do you really want to delete this integrator?',
        'name' => 'Name',
        'clear_name' => 'Clear integrator\'s name',
        'main_info' => 'Main info',
        'info' => 'Placeholder information',
        'cancel' => 'Cancel',
        'removeConfirm' => 'Do you really want to remove this block?',
        'removeSuccess' => 'Placeholder deleted',
        'placeholderSaved' => 'Placeholder successfully saved.',
        'placeholderSuccessfullyUpdated' => 'Placeholder successfully updated',

        'identifier' => 'Identifier',
        'description' => 'Description',
        'position' => 'Position',
        'size' => 'Size',
        'widthOptions' => 'Width options',
        'percent' => 'Percent',
        'ratio' => 'Coefficient',
        'modulo' => 'Modulo',
        'widthValue' => 'Width value',
        'heightOptions' => 'Height options',
        'heightValue' => 'Height value',

        'serverView' => 'Server view',
        'unessentialField' => 'Unessential field',

        'attributes' => 'Attributes',
        'miniBlocks' => 'Mini Blocks',

        'addNewAttribute' => 'Add new attribute',
        'addtitionalBlockCSSProperties' => 'Additional block CSS properties',
        'key' => 'Key',
        'value' => 'Value',
        'saveAttribute' => 'Save attribute',
        'removeAttribute' => 'Remove attribute',
        'attributeRemoved' => 'Attribute removed',
        'removeAttributeConfirm' => 'Do you really want to remove this attribute?',
        'addRelatedMiniBlock' => 'Add related mini block',
        'relatedMiniBlocks' => 'Related mini blocks',
        'miniBlock' => 'Mini block',
        'miniBlockName' => 'Mini block name',
        'saveRelationWithMiniBlock' => 'Save relation with mini block',
        'removeRelationWithMiniBlock' => 'Remove relation with mini block',
        'miniBlockDeletedSuccess' => 'Mini block was deleted',
        'removeRelationWithProductBlock' => 'Remove relation with product block',
        'addProductBlock' => 'Add product block',
        'relatedProductBlocks' => 'Related product blocks',
        'productBlock' => 'Product block',
        'productBlocks' => 'Product blocks',
        'saveRelationWithProductBlock' => 'Save relation with product Block',
        'chooseProductBlock' => 'Please, choose product block',
        'productBlockRelationSaveSuccess' => 'Product block relation was successfully saved.',

        'placeholder' => 'Placeholder',
        'placeholderName' => 'Placeholder name',
        'addPlaceholder' => 'Add placeholder',
        'savePlaceholder' => 'Save placeholder',
        'savedPlaceholder' => 'Placeholder saved',
        'removePlaceholder' => 'Delete placeholder',
        'removePlaceholderConfirm' => 'Do you really want to remove this placeholder?',
        'integratorPlaceholders' => 'Integrator placeholders',
        'noPlaceholders' => 'There are no placeholders related to this integrator.',
        'removePlaceholderError' => 'Failed to remove placeholder.',
    ),

    'miniBlock' => array(
        'add' => 'Create new mini block',
        'name' => 'Name',
        'upd' => 'Update mini block',
        'remove' => 'Remove mini block',
        'set' => 'Set mini block',
        'info' => 'Mini block information',
        'positionAndRelatedComponent' => 'Position and related component',
        'position' => 'Position',
        'componentName' => 'Component name',
        'pathToViewFile' => 'Path to view file',
        'useTheBackground' => 'Use the background?',
        'componentBehaviour' => 'Component Behaviour',
        'generalInfo' => 'General info',
        'miniBlockName' => 'Mini block name',
        'miniBlockButtonName' => 'Mini block button name',
        'miniBlockDeleteSuccess' => 'Mini block successfully deleted',
        'removeMiniBlockConfirmation' => 'Do you really want to delete mini block?'
    ),

    'productBlock' => array(
        'add' => 'Create new product block',
        'name' => 'Name',
        'upd' => 'Update product block',
        'remove' => 'Remove product block',
    ),

    'static_comp' => array(
        'info' => 'Static component information',
        'seo' => 'SEO parametrs (meta)',
        'date' => 'Publication date',
        'title' => 'Title',
        'author' => 'Author',
        'description' => 'Description',
        'link' => 'Link',
        'seo_title' => 'Seo title',
        'key_words' => 'Key words',
        'seo_description' => 'Seo description',
    ),

    'menu_item' => array(
        'add' => 'Add the menu item',
        'upd' => 'Update the menu item',
        'remove' => 'Remove the menu item',
        'info' => 'Information about menu item',
        'parent' => 'Parent menu item',
        'disconnect' => 'Disconnect menu item from component',
        'component' => 'Component',
        'main' => 'Open menu item in main',
        'neighborhood' => 'Show neighborhood',
        'open_in_tab' => 'Open menu item in tab',
        'name' => 'Menu item name',
        'conf_remove' => 'Are you sure want delete menu item',
    ),

    'lang' => array(
        'add' => 'Add language',
        'upd' => 'Update language',
        'remove' => 'Remove language',
        'conf_remove' => 'Remove language',
        'info' => 'Language info',
        'name' => 'Language name',
        'iso_code' => 'ISO code',
    ),

    'user' => array(
        'add' => 'Add user',
        'upd' => 'Update user',
        'remove' => 'Remove user',
        'conf_remove' => 'Are you sure to delete user ',
        'info' => 'Information about user',
        'reg_date' => 'Registration date',
        'last_login' => 'Last login',
        'name' => 'Name',
        'surname' => 'Surname',
        'username' => 'Username',
        'email' => 'E-mail',
        'password' => 'Password',
        'conf_password' => 'Confirm password',
        'phone' => 'Phone',
        'group' => 'Group',

    ),

    'article' => array(
        'add' => 'Add article item',
        'upd' => 'Update article item',
        'remove' => 'Remove article item',
        'removed' => 'Article item successfuly removed',
        'conf_remove' => 'Do you want remove the article item?',
        'description' => 'Article description',
        'desc_top' => 'Article description top',
        'desc_bottom' => 'Article description bottom',
        'article_seo' => 'Article seo parametrs',
        'link' => 'Link',
        'seo_title' => 'Seo title',
        'key_words' => 'Key words',
        'seo_description' => 'Seo description',
        'article_items' => 'Article items list',
        'page_settings' => 'Article page settings',
    ),

    'article_item' => array(
        'info' => 'Article item info',
        'seo' => 'Article item seo parametrs',
        'date' => 'General article item info',
        'date_time' => 'Publication date',
        'finished' => 'Is article finished?',
        'published' => 'Published',
        'main' => 'Maxi article',
        'img' => 'Article image',
        'title' => 'Title',
        'author' => 'Author',
        'description' => 'Description',
        'link' => 'Link',
        'seo_title' => 'Seo title',
        'key_words' => 'Key words',
        'seo_description' => 'Seo description',
    ),

    'seo' => array(
        'info' => 'The content of meta-tags home page',
        'title' => 'Title',
        'key_words' => 'Key words',
        'description' => 'Description',
    ),

    'group' => array(
        'info' => 'Group information',
        'add' => 'Add group',
        'upd' => 'Update group',
        'remove' => 'Remove group',
        'conf_remove' => 'Do you want remove group',
        'name' => 'Name',
        'sys_name' => 'System name',
        'admin_access' => 'Admin panel access',
        'description' => 'Description',
        'permissions' => 'Group permissions',
        'static' => 'Static tab components',
        'dynamic' => 'Dynamic components',
    ),

    'banner' => array(
        'info' => 'Banner info',
        'add' => 'Add banner',
        'upd' => 'Update banner',
        'remove' => 'Remove banner',
        'conf_remove' => 'Do you really want remove banner',
        'conf_remove_img' => 'Do you really want remove banner image',
        'param' => 'Banner settings',
        'position' => 'Position',
        'display' => 'Ready',
        'top' => 'Top',
        'left' => 'Left',
        'width' => 'Width',
        'height' => 'Height',
        'title' => 'Title',
        'description' => 'Description',
        'img' => 'Image',
    ),

    'marking' => array(
        'info' => 'Page layout settings',
        'min_width' => 'Minimal width',
        'max_width' => 'Maximal width',
        'width' => 'Width',
        'height' => 'Height',
        'min_font_size' => 'Font size',
    ),

    'validation' => array(
        'required' => 'This field is required.',
        'minlength' => 'Please enter at least {0} characters.',
        'maxlength' => 'Please enter no more than {0} characters.',
        'number' => 'Please enter a valid number.',
        'digits' => 'Please enter an integer.',
        'range' => 'Please enter a value between {0} and {1} characters long.',
        'email' => 'Please enter a valid email address.',
        'unique_email' => 'This email is already registered.',
        'unique_link' => 'This link already registered.',
        'regexp' => 'Can only contain letters, numbers and the underscore.',
        'equal_to' => 'Please enter the same value again.'
    ),

//        'main_page' => 'Zuhause', //Главная страница
//        'save' => 'Speichern', //Сохранить изменения
//        'cancel' => 'Cancel', //Отмена
//        'name' => 'Name', //Название
//        'action' => 'Tätigkeit', //Действие
//        'errorCode' => 'Der Fehlercode', //Код ошибки
//        'error' => 'Fehler', //Ошибка
//        'saved' => 'Änderungen wurden erfolgreich durchgeführt',
//        'deleted' => 'Entfernt', //Удалено
//        'deleteError' => 'Fehler Removal', //Ошибка удаления
//        'yes' => 'Ja',
//        'no' => 'Nicht',
//        'logout' => 'Abmeldung',
//        'next' => 'Weiter',
//        'not_auth' => 'Unauthorized',
//        'loginData' => 'Zugangsdaten',
//        'on_page' => 'auf Seite',
//        'back' => '« zurück',
//        'next' => 'weiter »',
//        'kitchenator' => 'Kitchenator',
//        'configuration_add' => 'eine Konfiguration erstellen',
//        'configuration_your' => 'Ihre Konfiguration',
//        'back_to_list' => '« Zurück zur Liste',
//        'not_complete' => 'Sie otveteli nicht alle obligatorischen Fragen',
//
//        'question' => array(
//            'add' => 'Frage hinzufügen',
//            'upd' => 'Frage ändern', //Изменение вопроса
//            'delete' => 'Frage entfernen',
//            'delete_conf' => 'Wollen Sie wirklich, um die Frage zu löschen',
//            'info' => 'Informationen über die Frage', // Информация о вопросе
//            'type' => 'Fragentyp',
//            'types' => array('radio', 'checkbox', 'text', 'upload'),
//            'position' => 'Position',
//            'required' => 'erforderlich',
//            'comment' => 'Kommentar',
//            'cols' => 'Spalten',
//            'question' => 'Frage',
//            'questions' => 'Fragen',
//            'desc' => 'Erläuterung',
//            'regular_info' => 'Informationen über reguläre Ausdrücke Ausgabe', // Информация о регулярном выражении вопроса
//            'variants' => 'Varianten der Antworten',
//            'variant_add' => 'Anwortvariante hinzufügen', //Добавить вариант ответа
//            'variant_edit' => 'Bearbeiten beantworten', //Редактировать вариант ответа
//            'variant_delete' => 'Entfernen beantworten', //Удалить вариант ответа
//            'variant_delete_conf' => 'Sind Sie sicher, dass Sie diese Antwort löschen', //Вы действительно хотите удалить этот вариант ответа
//            'variant_name' => 'Antwort im Fragebogen',
//            'variant_desc' => 'Antwort in der PV',
//            'variant_active' => 'Tätig',
//            'conf_remove_logo' => 'Sind Sie sicher, dass Sie das Logo zu entfernen',
//            'reqular' => 'Der reguläre Ausdruck', //Регулярное выражение
//            'text' => 'Text',
//            'orderSaved' => 'Die Bestellung wird gespeichert', //Порядок сохранен
//            'page' => 'Seite',
//            'page_add' => 'hinzufügen einer Seite',
//            'page_delete_confirm' => 'Sie wirklich wollen, um die Seite zu löschen'
//        ),
//
//
//        'formular' => array(
//            'form' => 'Allgemeine Informationen',
//            'question' => 'Fragebogen',
//            'add' => 'Formular hinzufügen',
//            'upd' => 'Formular ändern',
//            'remove' => 'Formular löschen',
//            'info' => 'Informationen über die Formular',
//            'start_date' => 'Startdatum',
//            'end_date' => 'Enddatum',
//            'new' => 'Neue',
//            'send' => 'Senden',
//            'sent' => 'Post',
//            'status' => 'Status',
//            'doc_all' => 'Alle zusammen',
//            'doc_quest' => 'Auf Frage',
//            'down_quest' => 'Reload',
//            'statuses' => array('Nicht fertig', 'Fertig', 'Bezahlt (paypal/visa/mastercard)', 'Bezahlt Rechnung', 'Erwarted (paypal/visa/mastercard)', 'Erwarted Rechnung', 'Abgeschlossen'),
////            'statuses' => array('Не готово', 'Готово', 'Оплачено (paypal/visa/mastercard)', 'Оплачено по счету', 'В ожидании (paypal/visa/mastercard)', 'В ожидании перевод', 'Окончено'),
//            'send_statuses' => array('Nicht angefordet', 'Angefordet', 'Gesendet', 'Empfangen'),
////            'send_statuses' => array('Не требует отправку по почте', 'отправить по почте', 'Отправлено', 'Получено'),
//            'document' => 'Documente',
//            'make_pdf' => 'Machen pdf',
//            'text' => 'Text',
//            'comment' => 'Wünsche',
//        ),
//
//        'user' => array(
//            'add' => 'Benutzer hinzufügen',
//            'upd' => 'aktualisieren Benutzer',
//            'remove'=> 'entfernen von Benutzer',
//            'info' => 'Benutzer-Informationen',
//            'last_enter' => 'Letzte Anmeldung',
//            'first_name' => 'Vorname',
//            'last_name' => 'Nachname',
//            'born_date' => 'Geburtsdatum',
//            'city' => 'Stadt',
//            'address' => 'Adresse',
//            'phone' => 'TEL',
//            'group' => 'Gruppe',
//            'login' => 'Anmelden',
//            'password' => 'Passwort',
//            'conf_password' => 'Passwort nochmal',
//            'user' => 'Benutzer',
//            'formular' => 'Formulare',
//            'reg_date' => 'Datum der Eintragung',
//            'name' => 'Vorname Nachname',
//            'email' => 'E-mail',
//            'conf_email' => 'E-mail nochmal',
//            'treatment' => 'Anrede',
//            'degree' => 'Titel',
//            'treatmentArr' => array('Herr', 'Frau'),
//            'degreeArr' => array('Professor', 'Dr. Professor', 'Doctor'),
//            'login_data' => 'Anmelden Daten',
//            'aktiv' => 'Aktive Benutzer'
//        ),
//
//        'seo' => array(
//            'info' => 'Der Inhalt des Meta-Tags Startseite',
//            'title' => 'Titel',
//            'key_words' => 'Keywords',
//            'description' => 'Beschreibung'
//        ),
//
//
//        'menu_item' => array(
//            'add' => 'Menüpunkt hinzufügen',
//            'upd' => 'Menüpunkt ändern',
//            'delete' => 'Menüpunkt entfernen',
//            'info' => 'Informationen über den Menüpunkt',
//            'del_component' => 'Trennen Sie den Menü',
//            'parent' => 'Das übergeordnete Menüpunkt',
//            'component' => 'Komponente',
//            'exist_component' => 'Die Komponente, die Sie hinzufügen möchten, ist bereits vorhanden',
//            'position' => 'Position',
//        ),
//
//        'static' => array(
//            'info' => 'Informationen über den Artikel',
//            'date' => 'Datum',
//            'time' => 'Zeit',
//            'title' => 'Titel',
//            'description' => 'Beschreibung',
//            'meta' => 'Der Inhalt des Meta-Tags',
//            'link' => 'Link',
//            'key_words' => 'Keywords',
//            'seo_param' => 'Seo Seite Einstellungen'
//        )

    );

?>
