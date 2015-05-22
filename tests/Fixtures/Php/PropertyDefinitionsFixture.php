<?php

$id = new \Dkd\PhpCmis\DataObjects\PropertyIdDefinition('cmis:id');
$id->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::ID));
$id->setLocalName('cmis:idValue');
$id->setQueryName('cmis:idValue');
$id->setIsInherited(false);
$id->setIsOpenChoice(false);
$id->setIsOrderable(true);
$id->setDescription('This is a id property.');
$id->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READONLY));
$id->setLocalNamespace('local');
$id->setDisplayName('Id property');
$id->setIsRequired(true);
$id->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::SINGLE));
$id->setIsQueryable(true);

$string = new \Dkd\PhpCmis\DataObjects\PropertyStringDefinition('cmis:string');
$string->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::STRING));
$string->setLocalName('cmis:stringValue');
$string->setQueryName('cmis:stringValue');
$string->setIsInherited(true);
$string->setIsOpenChoice(true);
$string->setIsOrderable(false);
$string->setDescription('This is a string property.');
$string->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$string->setLocalNamespace('namespace');
$string->setDisplayName('String property');
$string->setIsRequired(false);
$string->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$string->setIsQueryable(false);
$string->setMaxLength(100);

$boolean = new \Dkd\PhpCmis\DataObjects\PropertyBooleanDefinition('cmis:boolean');
$boolean->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::BOOLEAN));
$boolean->setLocalName('cmis:booleanValue');
$boolean->setQueryName('cmis:booleanValue');
$boolean->setIsInherited(true);
$boolean->setIsOpenChoice(true);
$boolean->setIsOrderable(false);
$boolean->setDescription('This is a boolean property.');
$boolean->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$boolean->setLocalNamespace('namespace');
$boolean->setDisplayName('Boolean property');
$boolean->setIsRequired(false);
$boolean->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$boolean->setIsQueryable(true);

$uri = new \Dkd\PhpCmis\DataObjects\PropertyUriDefinition('cmis:uri');
$uri->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::URI));
$uri->setLocalName('cmis:uriValue');
$uri->setQueryName('cmis:uriValue');
$uri->setIsInherited(true);
$uri->setIsOpenChoice(true);
$uri->setIsOrderable(false);
$uri->setDescription('This is a uri property.');
$uri->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$uri->setLocalNamespace('namespace');
$uri->setDisplayName('Uri property');
$uri->setIsRequired(false);
$uri->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$uri->setIsQueryable(true);

$decimal = new \Dkd\PhpCmis\DataObjects\PropertyDecimalDefinition('cmis:decimal');
$decimal->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::DECIMAL));
$decimal->setLocalName('cmis:decimalValue');
$decimal->setQueryName('cmis:decimalValue');
$decimal->setIsInherited(true);
$decimal->setIsOpenChoice(true);
$decimal->setIsOrderable(false);
$decimal->setDescription('This is a decimal property.');
$decimal->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$decimal->setLocalNamespace('namespace');
$decimal->setDisplayName('Decimal property');
$decimal->setIsRequired(false);
$decimal->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$decimal->setIsQueryable(true);
$decimal->setMinValue(5);
$decimal->setMaxValue(15);
$decimal->setPrecision(\Dkd\PhpCmis\Enum\DecimalPrecision::cast(\Dkd\PhpCmis\Enum\DecimalPrecision::BITS64));

$html = new \Dkd\PhpCmis\DataObjects\PropertyHtmlDefinition('cmis:html');
$html->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::HTML));
$html->setLocalName('cmis:htmlValue');
$html->setQueryName('cmis:htmlValue');
$html->setIsInherited(true);
$html->setIsOpenChoice(true);
$html->setIsOrderable(false);
$html->setDescription('This is a html property.');
$html->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$html->setLocalNamespace('namespace');
$html->setDisplayName('Html property');
$html->setIsRequired(false);
$html->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$html->setIsQueryable(true);

$integer = new \Dkd\PhpCmis\DataObjects\PropertyIntegerDefinition('cmis:integer');
$integer->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::INTEGER));
$integer->setLocalName('cmis:integerValue');
$integer->setQueryName('cmis:integerValue');
$integer->setIsInherited(true);
$integer->setIsOpenChoice(true);
$integer->setIsOrderable(false);
$integer->setDescription('This is a integer property.');
$integer->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$integer->setLocalNamespace('namespace');
$integer->setDisplayName('Integer property');
$integer->setIsRequired(false);
$integer->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$integer->setIsQueryable(true);
$integer->setMinValue(5);
$integer->setMaxValue(100);

$datetime = new \Dkd\PhpCmis\DataObjects\PropertyDateTimeDefinition('cmis:datetime');
$datetime->setPropertyType(\Dkd\PhpCmis\Enum\PropertyType::cast(\Dkd\PhpCmis\Enum\PropertyType::DATETIME));
$datetime->setLocalName('cmis:datetimeValue');
$datetime->setQueryName('cmis:datetimeValue');
$datetime->setIsInherited(true);
$datetime->setIsOpenChoice(true);
$datetime->setIsOrderable(false);
$datetime->setDescription('This is a datetime property.');
$datetime->setUpdatability(\Dkd\PhpCmis\Enum\Updatability::cast(\Dkd\PhpCmis\Enum\Updatability::READWRITE));
$datetime->setLocalNamespace('namespace');
$datetime->setDisplayName('Datetime property');
$datetime->setIsRequired(false);
$datetime->setCardinality(\Dkd\PhpCmis\Enum\Cardinality::cast(\Dkd\PhpCmis\Enum\Cardinality::MULTI));
$datetime->setIsQueryable(true);
$datetime->setDateTimeResolution(
    \Dkd\PhpCmis\Enum\DateTimeResolution::cast(\Dkd\PhpCmis\Enum\DateTimeResolution::TIME)
);

return array(
    'cmis:id' => $id,
    'cmis:string' => $string,
    'cmis:boolean' => $boolean,
    'cmis:uri' => $uri,
    'cmis:decimal' => $decimal,
    'cmis:html' => $html,
    'cmis:integer' => $integer,
    'cmis:datetime' => $datetime
);
