Создание заказа

Для доступа к методу необходимо разрешение order_write.

Метод создает заказ и возвращает внутренний ID созданного заказа.

Если не указывать order[createdAt], то будет использовано текущее время в качестве даты/времени оформления заказа.

Если требуется привязать заказ к существующему клиенту, то необходимо передать внешний ID клиента в поле order[customer][externalId], внутренний ID клиента в поле order[customer][id] либо идентификатор клиента в Daemon Collector в поле order[customer][browserId]. Поиск клиента будет осуществляться в рамках магазинов, к которым есть доступ у используемого API-ключа. Если не указывать order[customer], то клиент будет автоматически создан на основе данных из заказа.

Поле contragent[contragentType] может принимать 3 значения: individual - физическое лицо, legal-entity - юридическое лицо, enterpreneur - индивидуальный предприниматель. Для различных типов юр. лиц доступны различные наборы полей. Для типа individual недоступны все поля, для типа legal-entity недоступны поля contragent[OGRNIP], contragent[certificateNumber], contragent[certificateDate], для типа enterpreneur недоступны поля contragent[OGRN], contragent[KPP].

В полях order[orderType], order[orderMethod], order[payments][][type], order[payments][][status], order[status], order[shipmentStore], order[delivery][code], order[items][][status] указывается символьный код элемента.

В полях order[managerId], order[sourceId] указывается внутренний ID сущности в системе.

Нельзя изменять комментарий order[statusComment] без изменения статуса заказа order[status].

Товары заказа указываются в поле order[items][]. Не переданные в запросе на редактирование товары удаляются из заказа. Если товар присутствует в каталоге, то необходимо установить значение одного из следующих полей:

    order[items][][offer][id] – внутренний ID торгового предложения;
    order[items][][offer][externalId] – внешний ID товара или торгового предложения (SKU);
    order[items][][offer][xmlId] – ID торгового предложения в складской системе.

Если установлено значение нескольких полей, они будут обрабатываться в указанном выше порядке.

В случае, если ни один из идентификаторов товара не передан либо товар не найден, то товар будет автоматически создан на основе данных полей order[items][][initialPrice], order[items][][purchasePrice], order[items][][productName], при этом данная позиция товара в заказе не привязывается к товару в каталоге.

Адрес доставки order[delivery][address] можно указывать либо в строковом виде в поле order[delivery][address][text], либо в подробном виде, заполняя все поля кроме order[delivery][address][text].

В поле order[customFields] можно передавать массив значений пользовательских полей. Для полей типа «Справочник» указывается символьный код значения в справочнике. Для полей типа «Дата» указывается дата в формате Y-m-d. Для других типов полей указывается непосредственно значение.

Для работы с типами цен необходимо, чтобы в справочнике было активно более одного типа цен. Для передачи типа цены для товарной позиции в заказе необходимо передать код нужного типа цен в поле order[items][][priceType][code]. Рекомендуется вместе с типом цены передавать актуальное значение цены товара через order[items][][initialPrice]. Если передать тип цены order[items][][priceType][code] без значения цены order[items][][initialPrice], то в качестве цены товарной позиции возьмется текущее значения данного типа цен для данного товара. Для новой товарной позиции рекомендуется всегда передавать цену order[items][][initialPrice] явно, на случай если актуальная цена еще не была загружена в систему. Если для товара не передать тип цены order[items][][priceType][code], то в карточке заказа для товарной позиции в типе цены будет указанно Без типа. В случае, если в системе используется только базовый тип цен, то параметр order[items][][priceType][code] следует опустить.

Порядок позиций заказа order[items][] сохраняется в ответе.

Параметры order[items][][externalId] и order[items][][externalIds] являются не обязательными.

Одновременно можно указывать или значение внешнего идентификатора order[items][][externalId] или массив внешних идентификаторов order[items][][externalIds].

Значение внешнего идентификатора order[items][][externalId] будет записано в массив order[items][][externalIds] с кодом default.

Значения внешних идентификаторов order[items][][externalIds][][value] должны быть уникальны по коду order[items][][externalIds][][code] в пределах одного заказа.

Поле privilegeType может содержать одно из следующих значений:

    personal_discount - персональная скидка клиента;
    loyalty_level - расчет скидки или начисление бонусов исходя из настроек уровня ПЛ клиента;
    loyalty_event - расчет скидки по событию ПЛ;
    none - не применять скидки ПЛ к заказу;

Для применения Программы лояльности к заказу необходимо чтобы соблюдались условия:

    магазин заказа совпадал с магазином нужной ПЛ;
    у клиента в заказе было создано Участие в ПЛ;
    указано поле privilegeType;

Если в privilegeType указано значение loyalty_event и событие дает скидку, то необходимо указать ID в поле loyaltyEventDiscountId
Параметры
Параметр	Тип	Формат	Описание
site	string		Символьный код магазина
order	object (SerializedOrder)		
order[number]	string		Номер заказа
order[externalId]	string		Внешний ID заказа
order[privilegeType]	string		Тип привилегии
order[countryIso]	string		ISO код страны
order[createdAt]	DateTime	Y-m-d H:i:s	Дата оформления заказа
order[statusUpdatedAt]	DateTime	Y-m-d H:i:s	Дата последнего изменения статуса
order[discountManualAmount]	double		Денежная скидка на весь заказ (в валюте объекта)
order[discountManualPercent]	double		Процентная скидка на весь заказ
order[mark]	integer		Оценка заказа
order[markDatetime]	DateTime	Y-m-d H:i:s	Дата и время получение оценки от покупателя
order[lastName]	string		Фамилия
order[firstName]	string		Имя
order[patronymic]	string		Отчество
order[phone]	string		Телефон
order[additionalPhone]	string		Дополнительный телефон
order[email]	string		E-mail
order[call]	boolean		Требуется позвонить
order[expired]	boolean		Просрочен
order[customerComment]	string		Комментарий клиента
order[managerComment]	string		Комментарий оператора
order[contragent]	object (OrderContragent)		Реквизиты
order[contragent][contragentType]	string		Тип контрагента
order[contragent][legalName]	string		Полное наименование
order[contragent][legalAddress]	string		Адрес регистрации
order[contragent][INN]	string		ИНН
order[contragent][OKPO]	string		ОКПО
order[contragent][KPP]	string		КПП
order[contragent][OGRN]	string		ОГРН
order[contragent][OGRNIP]	string		ОГРНИП
order[contragent][certificateNumber]	string		Номер свидетельства
order[contragent][certificateDate]	DateTime	Y-m-d	Дата свидетельства
order[contragent][BIK]	string		БИК
order[contragent][bank]	string		Банк
order[contragent][bankAddress]	string		Адрес банка
order[contragent][corrAccount]	string		Корр. счёт
order[contragent][bankAccount]	string		Расчётный счёт
order[statusComment]	string		Комментарий к последнему изменению статуса
order[weight]	double		Вес
order[length]	integer		Длина
order[width]	integer		Ширина
order[height]	integer		Высота
order[shipmentDate]	DateTime	Y-m-d	Дата отгрузки
order[shipped]	boolean		Заказ отгружен
order[dialogId]	object (MGDialog)		Идентификатор диалога Чатов
order[customFields]	array		Ассоциативный массив пользовательских полей
order[orderType]	string		Тип заказа
order[orderMethod]	string		Способ оформления
order[customer]	object (SerializedRelationCustomer)		Клиент
order[customer][id]	integer		Внутренний ID клиента
order[customer][externalId]	string		Внешний ID клиента
order[customer][browserId]	string		Идентификатор устройства в Collector
order[customer][site]	string		Код магазина, необходим при передаче externalId
order[customer][type]	string		Тип клиента (передаётся когда нужно создать нового клиента)
order[customer][nickName]	string		Наименование корпоративного клиента (передаётся когда нужно создать нового корпоративного клиента)
order[contact]	object (SerializedRelationAbstractCustomer)		Контактное лицо
order[contact][id]	integer		Внутренний ID клиента
order[contact][externalId]	string		Внешний ID клиента
order[contact][browserId]	string		Идентификатор устройства в Collector
order[contact][site]	string		Код магазина, необходим при передаче externalId
order[company]	object (EntityWithExternalIdInput)		Компания
order[company][id]	integer		ID
order[company][externalId]	string		Внешний ID
order[managerId]	integer		Менеджер, прикрепленный к заказу
order[status]	string		Статус заказа
order[items][]	array of objects (SerializedOrderProduct)		
order[items][][markingCodes][]	array of strings		deprecated Коды маркировки. Используйте markingObjects
order[items][][markingObjects][]	array of objects (SerializedOrderProductMarkingCode)		Данные маркировки
order[items][][markingObjects][][code]	string		Значение кода маркировки
order[items][][markingObjects][][provider]	string		Тип кода маркировки. Возможные значения: chestny_znak, giis_dmdk
order[items][][initialPrice]	double		Цена товара/SKU (в валюте объекта)
order[items][][discountManualAmount]	double		Денежная скидка на единицу товара (в валюте объекта)
order[items][][discountManualPercent]	double		Процентная скидка на единицу товара
order[items][][vatRate]	string		Ставка НДС
order[items][][createdAt]	DateTime	Y-m-d H:i:s	Дата создания позиции в системе
order[items][][quantity]	float		Количество
order[items][][comment]	string		Комментарий к позиции в заказе
order[items][][properties][]	array		[массив] Дополнительные свойства позиции в заказе
order[items][][properties][][code]	string	{not blank}{match: /^[a-zA-Z0-9_][a-zA-Z0-9_\-:]*$/D}}	Код свойства (не обязательное поле, код может передаваться в ключе свойства)
order[items][][properties][][name]	string	{not blank}	Имя свойства
order[items][][properties][][value]	string	{not blank}	Значение свойства
order[items][][purchasePrice]	double		Закупочная цена (в базовой валюте)
order[items][][ordering]	integer		Порядок
order[items][][offer]	object (SerializedOrderProductOffer)		Торговое предложение
order[items][][offer][id]	integer		ID торгового предложения
order[items][][offer][externalId]	string		Внешний ID торгового предложения
order[items][][offer][xmlId]	string		ID торгового предложения в складской системе
order[items][][productName]	string		Название товара
order[items][][status]	string		Статус позиции в заказе
order[items][][priceType]	object (PriceType)		Тип цены
order[items][][priceType][code]	string		Код типа цены
order[items][][externalId]	string		deprecated Внешний ID позиции в заказе
order[items][][externalIds][]	array of objects (CodeValueModel)		Внешние идентификаторы позиции в заказе
order[items][][externalIds][][code]	string		Код
order[items][][externalIds][][value]	string		Значение
order[delivery]	object (SerializedOrderDelivery)		Данные о доставке
order[delivery][code]	string		Код типа доставки
order[delivery][data]			Данные службы доставки, подключенной через API
order[delivery][data][externalId]	string		Идентификатор в службе доставки
order[delivery][data][trackNumber]	string		Номер отправления (поле deprecated на запись)
order[delivery][data][locked]	boolean		Не синхронизировать со службой доставки
order[delivery][data][tariff]	string		Код тарифа
order[delivery][data][pickuppointId]	string		Идентификатор пункта самовывоза
order[delivery][data][payerType]	string		Плательщик за доставку
order[delivery][data][shipmentpointId]	string		Идентификатор терминала отгрузки
order[delivery][data][extraData]	array		Дополнительные данные доставки (deliveryDataField.code => значение)
order[delivery][data][itemDeclaredValues][]	array of objects (DeclaredValueItem)		
order[delivery][data][itemDeclaredValues][][orderProduct]	object (PackageItemOrderProduct)		Позиция в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][id]	integer		ID позиции в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][externalId]	string		deprecated Внешний ID позиции в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][externalIds][]	array of objects (CodeValueModel)		Внешние идентификаторы позиции в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][externalIds][][code]	string		Код
order[delivery][data][itemDeclaredValues][][orderProduct][externalIds][][value]	string		Значение
order[delivery][data][itemDeclaredValues][][value]	double		Объявленная стоимость товара
order[delivery][data][packages][]	array of objects (Package)		Упаковки
order[delivery][data][packages][][packageId]	string		Идентификатор упаковки
order[delivery][data][packages][][weight]	double		Вес г.
order[delivery][data][packages][][length]	integer		Длина мм.
order[delivery][data][packages][][width]	integer		Ширина мм.
order[delivery][data][packages][][height]	integer		Высота мм.
order[delivery][data][packages][][items][]	array of objects (PackageItem)		Содержимое упаковки
order[delivery][data][packages][][items][][orderProduct]	object (PackageItemOrderProduct)		Позиция в заказе
order[delivery][data][packages][][items][][orderProduct][id]	integer		ID позиции в заказе
order[delivery][data][packages][][items][][orderProduct][externalId]	string		deprecated Внешний ID позиции в заказе
order[delivery][data][packages][][items][][orderProduct][externalIds][]	array of objects (CodeValueModel)		Внешние идентификаторы позиции в заказе
order[delivery][data][packages][][items][][orderProduct][externalIds][][code]	string		Код
order[delivery][data][packages][][items][][orderProduct][externalIds][][value]	string		Значение
order[delivery][data][packages][][items][][quantity]	double		Количество товара в упаковке
order[delivery][service]	object (SerializedDeliveryService)		
order[delivery][service][name]	string		Название
order[delivery][service][code]	string		Символьный код
order[delivery][service][active]	boolean		Статус активности
order[delivery][service][deliveryType]	string		Тип доставки
order[delivery][cost]	double		Стоимость доставки
order[delivery][netCost]	double		Себестоимость доставки
order[delivery][date]	DateTime	Y-m-d	Дата доставки
order[delivery][time]	object (TimeInterval)		Информация о временном диапазоне
order[delivery][time][from]	DateTime	H:i	Время "с"
order[delivery][time][to]	DateTime	H:i	Время "до"
order[delivery][time][custom]	string		Временной диапазон в свободной форме
order[delivery][address]	object (OrderDeliveryAddress)		Адрес доставки
order[delivery][address][index]	string		Индекс
order[delivery][address][countryIso]	string		ISO код страны
order[delivery][address][region]	string		Регион
order[delivery][address][regionId]	integer		Идентификатор региона в Geohelper
order[delivery][address][city]	string		Город
order[delivery][address][cityId]	integer		Идентификатор города в Geohelper
order[delivery][address][cityType]	string		Тип населенного пункта
order[delivery][address][street]	string		Улица
order[delivery][address][streetId]	integer		Идентификатор улицы в Geohelper
order[delivery][address][streetType]	string		Тип улицы
order[delivery][address][building]	string		Дом
order[delivery][address][flat]	string		Номер квартиры/офиса
order[delivery][address][floor]	integer		Этаж
order[delivery][address][block]	integer		Подъезд
order[delivery][address][house]	string		Строение
order[delivery][address][housing]	string		Корпус
order[delivery][address][metro]	string		Метро
order[delivery][address][notes]	string		Примечания к адресу
order[delivery][address][text]	string		Адрес в текстовом виде
order[delivery][vatRate]	string		Ставка НДС
order[source]	object (SerializedSource)		Источник заказа
order[source][source]	string		Источник
order[source][medium]	string		Канал
order[source][campaign]	string		Кампания
order[source][keyword]	string		Ключевое слово
order[source][content]	string		Содержание кампании
order[shipmentStore]	string		Склад отгрузки
order[payments][]	array of objects (SerializedPayment)		Платежи
order[payments][][externalId]	string		Внешний ID платежа
order[payments][][amount]	double		Сумма платежа (в валюте объекта)
order[payments][][paidAt]	DateTime	Y-m-d H:i:s	Дата оплаты
order[payments][][comment]	string		Комментарий
order[payments][][type]	string		Тип оплаты
order[payments][][status]	string		Статус оплаты
order[loyaltyEventDiscountId]	integer		ID скидки по событию программы лояльности
order[applyRound]	boolean		Применять настройку округления стоимости заказа
order[isFromCart]	boolean		Заказ создан из корзины
Ответ
Параметр	Тип	Описание
success	boolean 	Результат запроса (успешный/неуспешный)
id	integer 	Внутренний ID созданного заказа
order	object (CreateOrder) 	
order[slug]	custom handler result for (int) 	deprecated Символьный код
order[bonusesCreditTotal]	double 	Количество начисленных бонусов
order[bonusesChargeTotal]	double 	Количество списанных бонусов
order[summ]	double 	Сумма по товарам/услугам (в валюте объекта)
order[currency]	string 	Валюта
order[id]	integer 	ID заказа
order[number]	string 	Номер заказа
order[externalId]	string 	Внешний ID заказа
order[orderType]	string 	Тип заказа
order[orderMethod]	string 	Способ оформления
order[privilegeType]	string 	Тип привилегии
order[countryIso]	string 	ISO код страны
order[createdAt]	DateTime 	Дата оформления заказа
order[statusUpdatedAt]	DateTime 	Дата последнего изменения статуса
order[totalSumm]	double 	Общая сумма с учетом скидки (в валюте объекта)
order[prepaySum]	double 	Оплаченная сумма (в валюте объекта)
order[purchaseSumm]	double 	Общая стоимость закупки (в базовой валюте)
order[personalDiscountPercent]	double 	Персональная скидка на заказ
order[loyaltyLevel]	object (LoyaltyLevel) 	Уровень участия в программе лояльности
order[loyaltyLevel][id]	integer 	ID уровня
order[loyaltyLevel][name]	string 	Название уровня
order[loyaltyEventDiscount]	object (LoyaltyEventDiscount) 	Скидка по событию программы лояльности
order[loyaltyEventDiscount][id]	integer 	ID
order[mark]	integer 	Оценка заказа
order[markDatetime]	DateTime 	Дата и время получение оценки от покупателя
order[lastName]	string 	Фамилия
order[firstName]	string 	Имя
order[patronymic]	string 	Отчество
order[phone]	string 	Телефон
order[additionalPhone]	string 	Дополнительный телефон
order[email]	string 	E-mail
order[call]	boolean 	Требуется позвонить
order[expired]	boolean 	Просрочен
order[customerComment]	string 	Комментарий клиента
order[managerComment]	string 	Комментарий оператора
order[managerId]	integer 	Менеджер, прикрепленный к заказу
order[customer]		Клиент
order[customer][type]	string 	Тип клиента
order[customer][id]	integer 	ID клиента
order[customer][externalId]	string 	Внешний ID клиента
order[customer][isContact]	boolean 	Клиент является контактным лицом (создан как контактное лицо и на него нет оформленных заказов)
order[customer][createdAt]	DateTime 	Создан
order[customer][managerId]	integer 	Менеджер клиента
order[customer][vip]	boolean 	Важный клиент
order[customer][bad]	boolean 	Плохой клиент
order[customer][site]	string 	Магазин, с которого пришел клиент
order[customer][contragent]	object (CustomerContragent) 	deprecated Реквизиты (Поля объекта следует использовать только при неактивированной функциональности "Корпоративные клиенты")
order[customer][contragent][contragentType]	string 	Тип контрагента
order[customer][contragent][legalName]	string 	Полное наименование
order[customer][contragent][legalAddress]	string 	Адрес регистрации
order[customer][contragent][INN]	string 	ИНН
order[customer][contragent][OKPO]	string 	ОКПО
order[customer][contragent][KPP]	string 	КПП
order[customer][contragent][OGRN]	string 	ОГРН
order[customer][contragent][OGRNIP]	string 	ОГРНИП
order[customer][contragent][certificateNumber]	string 	Номер свидетельства
order[customer][contragent][certificateDate]	DateTime 	Дата свидетельства
order[customer][contragent][BIK]	string 	БИК
order[customer][contragent][bank]	string 	Банк
order[customer][contragent][bankAddress]	string 	Адрес банка
order[customer][contragent][corrAccount]	string 	Корр. счёт
order[customer][contragent][bankAccount]	string 	Расчётный счёт
order[customer][tags][]	array of objects (CustomerTagLink) 	[массив] Теги
order[customer][tags][][color]	string 	
order[customer][tags][][name]	string 	
order[customer][tags][][colorCode]	string 	
order[customer][tags][][attached]	boolean 	
order[customer][firstClientId]	string 	Первая метка клиента Google Analytics
order[customer][lastClientId]	string 	Последняя метка клиента Google Analytics
order[customer][customFields]	array 	Ассоциативный массив пользовательских полей
order[customer][personalDiscount]	double 	Персональная скидка
order[customer][cumulativeDiscount]	double 	deprecated Накопительная скидка (Недоступно, начиная с 8 версии системы)
order[customer][discountCardNumber]	string 	Номер дисконтной карты
order[customer][avgMarginSumm]	float 	Средняя валовая прибыль по заказам клиента (в базовой валюте)
order[customer][marginSumm]	float 	LTV (в базовой валюте)
order[customer][totalSumm]	float 	Общая сумма заказов (в базовой валюте)
order[customer][averageSumm]	float 	Средняя сумма заказа (в базовой валюте)
order[customer][ordersCount]	integer 	Количество заказов
order[customer][costSumm]	float 	Сумма расходов (в базовой валюте)
order[customer][address]	object (CustomerAddress) 	Адрес клиента
order[customer][address][id]	integer 	ID адреса
order[customer][address][index]	string 	Индекс
order[customer][address][countryIso]	string 	ISO код страны
order[customer][address][region]	string 	Регион
order[customer][address][regionId]	integer 	Идентификатор региона в Geohelper
order[customer][address][city]	string 	Город
order[customer][address][cityId]	integer 	Идентификатор города в Geohelper
order[customer][address][cityType]	string 	Тип населенного пункта
order[customer][address][street]	string 	Улица
order[customer][address][streetId]	integer 	Идентификатор улицы в Geohelper
order[customer][address][streetType]	string 	Тип улицы
order[customer][address][building]	string 	Дом
order[customer][address][flat]	string 	Номер квартиры/офиса
order[customer][address][floor]	integer 	Этаж
order[customer][address][block]	integer 	Подъезд
order[customer][address][house]	string 	Строение
order[customer][address][housing]	string 	Корпус
order[customer][address][metro]	string 	Метро
order[customer][address][notes]	string 	Примечания к адресу
order[customer][address][text]	string 	Адрес в текстовом виде
order[customer][address][externalId]	string 	Внешний ID
order[customer][address][name]	string 	Наменование адреса
order[customer][segments][]	array of objects (Segment) 	[массив] Сегменты
order[customer][segments][][id]	integer 	Внутренний ID сегмента
order[customer][segments][][code]	string 	Символьный код
order[customer][segments][][name]	string 	Название сегмента
order[customer][segments][][createdAt]	DateTime 	Дата создания сегмента
order[customer][segments][][isDynamic]	boolean 	Является ли сегмент автоматически пересчитываемым
order[customer][segments][][customersCount]	integer 	Количество клиентов в сегменте
order[customer][segments][][active]	boolean 	Активность сегмента
order[customer][maturationTime]	integer 	Время «созревания», в секундах
order[customer][firstName]	string 	Имя
order[customer][lastName]	string 	Фамилия
order[customer][patronymic]	string 	Отчество
order[customer][sex]	string 	Пол
order[customer][presumableSex]	string 	Предполагаемый пол на основе ФИО
order[customer][email]	string 	E-mail
order[customer][emailMarketingUnsubscribedAt]	DateTime 	deprecated Дата отписки от email рассылок
order[customer][customerSubscriptions][]	array of objects (CustomerSubscription) 	Подписки
order[customer][customerSubscriptions][][subscription]	object (Subscription) 	Категория подписки
order[customer][customerSubscriptions][][subscription][id]	integer 	ID категории подписки
order[customer][customerSubscriptions][][subscription][channel]	string 	Канал
order[customer][customerSubscriptions][][subscription][name]	string 	Название
order[customer][customerSubscriptions][][subscription][code]	string 	Символьный код
order[customer][customerSubscriptions][][subscription][active]	boolean 	Статус активности
order[customer][customerSubscriptions][][subscription][autoSubscribe]	boolean 	Автоматически подписывать новых клиентов
order[customer][customerSubscriptions][][subscription][ordering]	integer 	Порядок
order[customer][customerSubscriptions][][subscribed]	boolean 	Активность подписки
order[customer][customerSubscriptions][][changedAt]	DateTime 	Дата изменения флага активности
order[customer][phones][]	array of objects (CustomerPhone) 	Телефоны
order[customer][phones][][number]	string 	Номер телефона
order[customer][birthday]	DateTime 	День рождения
order[customer][source]	object (SerializedSource) 	Источник клиента
order[customer][source][source]	string 	Источник
order[customer][source][medium]	string 	Канал
order[customer][source][campaign]	string 	Кампания
order[customer][source][keyword]	string 	Ключевое слово
order[customer][source][content]	string 	Содержание кампании
order[customer][mgCustomers][]	array of objects (MGCustomer) 	Клиенты MessageGateway
order[customer][mgCustomers][][id]	integer 	ID клиента
order[customer][mgCustomers][][externalId]	integer 	Внешний ID MessageGateway клиента
order[customer][mgCustomers][][mgChannel]	object (MGChannel) 	MessageGateway канал
order[customer][mgCustomers][][mgChannel][allowedSendByPhone]	custom handler result for (bool) 	Можно ли писать первыми в этот канал по номеру телефона
order[customer][mgCustomers][][mgChannel][id]	integer 	ID канала
order[customer][mgCustomers][][mgChannel][externalId]	integer 	Внешний ID канала
order[customer][mgCustomers][][mgChannel][type]	string 	Тип канала
order[customer][mgCustomers][][mgChannel][active]	boolean 	Активность канала
order[customer][mgCustomers][][mgChannel][name]	string 	Название канала
order[customer][photoUrl]	string 	URL фотографии
order[contact]	object (Customer) 	Контактное лицо
order[contact][type]	string 	Тип клиента
order[contact][id]	integer 	ID клиента
order[contact][externalId]	string 	Внешний ID клиента
order[contact][isContact]	boolean 	Клиент является контактным лицом (создан как контактное лицо и на него нет оформленных заказов)
order[contact][createdAt]	DateTime 	Создан
order[contact][managerId]	integer 	Менеджер клиента
order[contact][vip]	boolean 	Важный клиент
order[contact][bad]	boolean 	Плохой клиент
order[contact][site]	string 	Магазин, с которого пришел клиент
order[contact][contragent]	object (CustomerContragent) 	deprecated Реквизиты (Поля объекта следует использовать только при неактивированной функциональности "Корпоративные клиенты")
order[contact][contragent][contragentType]	string 	Тип контрагента
order[contact][contragent][legalName]	string 	Полное наименование
order[contact][contragent][legalAddress]	string 	Адрес регистрации
order[contact][contragent][INN]	string 	ИНН
order[contact][contragent][OKPO]	string 	ОКПО
order[contact][contragent][KPP]	string 	КПП
order[contact][contragent][OGRN]	string 	ОГРН
order[contact][contragent][OGRNIP]	string 	ОГРНИП
order[contact][contragent][certificateNumber]	string 	Номер свидетельства
order[contact][contragent][certificateDate]	DateTime 	Дата свидетельства
order[contact][contragent][BIK]	string 	БИК
order[contact][contragent][bank]	string 	Банк
order[contact][contragent][bankAddress]	string 	Адрес банка
order[contact][contragent][corrAccount]	string 	Корр. счёт
order[contact][contragent][bankAccount]	string 	Расчётный счёт
order[contact][tags][]	array of objects (CustomerTagLink) 	[массив] Теги
order[contact][tags][][color]	string 	
order[contact][tags][][name]	string 	
order[contact][tags][][colorCode]	string 	
order[contact][tags][][attached]	boolean 	
order[contact][firstClientId]	string 	Первая метка клиента Google Analytics
order[contact][lastClientId]	string 	Последняя метка клиента Google Analytics
order[contact][customFields]	array 	Ассоциативный массив пользовательских полей
order[contact][personalDiscount]	double 	Персональная скидка
order[contact][cumulativeDiscount]	double 	deprecated Накопительная скидка (Недоступно, начиная с 8 версии системы)
order[contact][discountCardNumber]	string 	Номер дисконтной карты
order[contact][avgMarginSumm]	float 	Средняя валовая прибыль по заказам клиента (в базовой валюте)
order[contact][marginSumm]	float 	LTV (в базовой валюте)
order[contact][totalSumm]	float 	Общая сумма заказов (в базовой валюте)
order[contact][averageSumm]	float 	Средняя сумма заказа (в базовой валюте)
order[contact][ordersCount]	integer 	Количество заказов
order[contact][costSumm]	float 	Сумма расходов (в базовой валюте)
order[contact][address]	object (CustomerAddress) 	Адрес клиента
order[contact][address][id]	integer 	ID адреса
order[contact][address][index]	string 	Индекс
order[contact][address][countryIso]	string 	ISO код страны
order[contact][address][region]	string 	Регион
order[contact][address][regionId]	integer 	Идентификатор региона в Geohelper
order[contact][address][city]	string 	Город
order[contact][address][cityId]	integer 	Идентификатор города в Geohelper
order[contact][address][cityType]	string 	Тип населенного пункта
order[contact][address][street]	string 	Улица
order[contact][address][streetId]	integer 	Идентификатор улицы в Geohelper
order[contact][address][streetType]	string 	Тип улицы
order[contact][address][building]	string 	Дом
order[contact][address][flat]	string 	Номер квартиры/офиса
order[contact][address][floor]	integer 	Этаж
order[contact][address][block]	integer 	Подъезд
order[contact][address][house]	string 	Строение
order[contact][address][housing]	string 	Корпус
order[contact][address][metro]	string 	Метро
order[contact][address][notes]	string 	Примечания к адресу
order[contact][address][text]	string 	Адрес в текстовом виде
order[contact][address][externalId]	string 	Внешний ID
order[contact][address][name]	string 	Наменование адреса
order[contact][segments][]	array of objects (Segment) 	[массив] Сегменты
order[contact][segments][][id]	integer 	Внутренний ID сегмента
order[contact][segments][][code]	string 	Символьный код
order[contact][segments][][name]	string 	Название сегмента
order[contact][segments][][createdAt]	DateTime 	Дата создания сегмента
order[contact][segments][][isDynamic]	boolean 	Является ли сегмент автоматически пересчитываемым
order[contact][segments][][customersCount]	integer 	Количество клиентов в сегменте
order[contact][segments][][active]	boolean 	Активность сегмента
order[contact][maturationTime]	integer 	Время «созревания», в секундах
order[contact][firstName]	string 	Имя
order[contact][lastName]	string 	Фамилия
order[contact][patronymic]	string 	Отчество
order[contact][sex]	string 	Пол
order[contact][presumableSex]	string 	Предполагаемый пол на основе ФИО
order[contact][email]	string 	E-mail
order[contact][emailMarketingUnsubscribedAt]	DateTime 	deprecated Дата отписки от email рассылок
order[contact][customerSubscriptions][]	array of objects (CustomerSubscription) 	Подписки
order[contact][customerSubscriptions][][subscription]	object (Subscription) 	Категория подписки
order[contact][customerSubscriptions][][subscription][id]	integer 	ID категории подписки
order[contact][customerSubscriptions][][subscription][channel]	string 	Канал
order[contact][customerSubscriptions][][subscription][name]	string 	Название
order[contact][customerSubscriptions][][subscription][code]	string 	Символьный код
order[contact][customerSubscriptions][][subscription][active]	boolean 	Статус активности
order[contact][customerSubscriptions][][subscription][autoSubscribe]	boolean 	Автоматически подписывать новых клиентов
order[contact][customerSubscriptions][][subscription][ordering]	integer 	Порядок
order[contact][customerSubscriptions][][subscribed]	boolean 	Активность подписки
order[contact][customerSubscriptions][][changedAt]	DateTime 	Дата изменения флага активности
order[contact][phones][]	array of objects (CustomerPhone) 	Телефоны
order[contact][phones][][number]	string 	Номер телефона
order[contact][birthday]	DateTime 	День рождения
order[contact][source]	object (SerializedSource) 	Источник клиента
order[contact][source][source]	string 	Источник
order[contact][source][medium]	string 	Канал
order[contact][source][campaign]	string 	Кампания
order[contact][source][keyword]	string 	Ключевое слово
order[contact][source][content]	string 	Содержание кампании
order[contact][mgCustomers][]	array of objects (MGCustomer) 	Клиенты MessageGateway
order[contact][mgCustomers][][id]	integer 	ID клиента
order[contact][mgCustomers][][externalId]	integer 	Внешний ID MessageGateway клиента
order[contact][mgCustomers][][mgChannel]	object (MGChannel) 	MessageGateway канал
order[contact][mgCustomers][][mgChannel][allowedSendByPhone]	custom handler result for (bool) 	Можно ли писать первыми в этот канал по номеру телефона
order[contact][mgCustomers][][mgChannel][id]	integer 	ID канала
order[contact][mgCustomers][][mgChannel][externalId]	integer 	Внешний ID канала
order[contact][mgCustomers][][mgChannel][type]	string 	Тип канала
order[contact][mgCustomers][][mgChannel][active]	boolean 	Активность канала
order[contact][mgCustomers][][mgChannel][name]	string 	Название канала
order[contact][photoUrl]	string 	URL фотографии
order[company]	object (Company) 	Компания
order[company][id]	integer 	ID компании
order[company][externalId]	string 	Внешний ID компании
order[company][customer]	object (SerializedEntityCustomer) 	Клиент
order[company][customer][site]	string 	Символьный код магазина
order[company][customer][id]	integer 	Внутренний ID клиента
order[company][customer][externalId]	string 	Внешний ID клиента
order[company][customer][type]	string 	Тип клиента
order[company][active]	boolean 	Активность
order[company][name]	string 	Наименование
order[company][brand]	string 	Бренд
order[company][site]	string 	Сайт компании
order[company][createdAt]	DateTime 	Дата создания
order[company][contragent]	object (CompanyContragent) 	Реквизиты
order[company][contragent][contragentType]	string 	Тип контрагента
order[company][contragent][legalName]	string 	Полное наименование
order[company][contragent][legalAddress]	string 	Адрес регистрации
order[company][contragent][INN]	string 	ИНН
order[company][contragent][OKPO]	string 	ОКПО
order[company][contragent][KPP]	string 	КПП
order[company][contragent][OGRN]	string 	ОГРН
order[company][contragent][OGRNIP]	string 	ОГРНИП
order[company][contragent][certificateNumber]	string 	Номер свидетельства
order[company][contragent][certificateDate]	DateTime 	Дата свидетельства
order[company][contragent][BIK]	string 	БИК
order[company][contragent][bank]	string 	Банк
order[company][contragent][bankAddress]	string 	Адрес банка
order[company][contragent][corrAccount]	string 	Корр. счёт
order[company][contragent][bankAccount]	string 	Расчётный счёт
order[company][address]	object (CustomerAddress) 	Адрес
order[company][address][id]	integer 	ID адреса
order[company][address][index]	string 	Индекс
order[company][address][countryIso]	string 	ISO код страны
order[company][address][region]	string 	Регион
order[company][address][regionId]	integer 	Идентификатор региона в Geohelper
order[company][address][city]	string 	Город
order[company][address][cityId]	integer 	Идентификатор города в Geohelper
order[company][address][cityType]	string 	Тип населенного пункта
order[company][address][street]	string 	Улица
order[company][address][streetId]	integer 	Идентификатор улицы в Geohelper
order[company][address][streetType]	string 	Тип улицы
order[company][address][building]	string 	Дом
order[company][address][flat]	string 	Номер квартиры/офиса
order[company][address][floor]	integer 	Этаж
order[company][address][block]	integer 	Подъезд
order[company][address][house]	string 	Строение
order[company][address][housing]	string 	Корпус
order[company][address][metro]	string 	Метро
order[company][address][notes]	string 	Примечания к адресу
order[company][address][text]	string 	Адрес в текстовом виде
order[company][address][externalId]	string 	Внешний ID
order[company][address][name]	string 	Наменование адреса
order[company][avgMarginSumm]	float 	Средняя валовая прибыль по заказам клиента (в базовой валюте)
order[company][marginSumm]	float 	LTV (в базовой валюте)
order[company][totalSumm]	float 	Общая сумма заказов (в базовой валюте)
order[company][averageSumm]	float 	Средняя сумма заказа (в базовой валюте)
order[company][costSumm]	float 	Сумма расходов (в базовой валюте)
order[company][ordersCount]	integer 	Количество заказов
order[company][customFields]	array 	Ассоциативный массив пользовательских полей
order[contragent]	object (OrderContragent) 	Реквизиты
order[contragent][contragentType]	string 	Тип контрагента
order[contragent][legalName]	string 	Полное наименование
order[contragent][legalAddress]	string 	Адрес регистрации
order[contragent][INN]	string 	ИНН
order[contragent][OKPO]	string 	ОКПО
order[contragent][KPP]	string 	КПП
order[contragent][OGRN]	string 	ОГРН
order[contragent][OGRNIP]	string 	ОГРНИП
order[contragent][certificateNumber]	string 	Номер свидетельства
order[contragent][certificateDate]	DateTime 	Дата свидетельства
order[contragent][BIK]	string 	БИК
order[contragent][bank]	string 	Банк
order[contragent][bankAddress]	string 	Адрес банка
order[contragent][corrAccount]	string 	Корр. счёт
order[contragent][bankAccount]	string 	Расчётный счёт
order[delivery]	object (SerializedOrderDelivery) 	Данные о доставке
order[delivery][code]	string 	Код типа доставки
order[delivery][integrationCode]	string 	Интеграционный код типа доставки
order[delivery][data]		Данные службы доставки, подключенной через API
order[delivery][data][externalId]	string 	Идентификатор в службе доставки
order[delivery][data][trackNumber]	string 	Номер отправления (поле deprecated на запись)
order[delivery][data][status]	string 	Код статуса доставки
order[delivery][data][locked]	boolean 	Не синхронизировать со службой доставки
order[delivery][data][pickuppointAddress]	string 	Адрес пункта самовывоза
order[delivery][data][days]	string 	Ориентировочный срок доставки
order[delivery][data][statusText]	string 	Наименование статуса доставки
order[delivery][data][statusDate]	DateTime 	Дата статуса доставки
order[delivery][data][tariff]	string 	Код тарифа
order[delivery][data][tariffName]	string 	Наименование тарифа
order[delivery][data][pickuppointId]	string 	Идентификатор пункта самовывоза
order[delivery][data][pickuppointSchedule]	string 	Режим работы пункта самовывоза
order[delivery][data][pickuppointPhone]	string 	Телефон пункта самовывоза
order[delivery][data][payerType]	string 	Плательщик за доставку
order[delivery][data][statusComment]	string 	Комментарий к статусу доставки
order[delivery][data][cost]	float 	Стоимость доставки, полученная из службы доставки (в валюте объекта)
order[delivery][data][minTerm]	integer 	Минимальный срок доставки
order[delivery][data][maxTerm]	integer 	Максимальный срок доставки
order[delivery][data][shipmentpointId]	string 	Идентификатор терминала отгрузки
order[delivery][data][shipmentpointName]	string 	Наименование терминала отгрузки
order[delivery][data][shipmentpointAddress]	string 	Адрес терминала отгрузки
order[delivery][data][shipmentpointSchedule]	string 	Режим работы терминала отгрузки
order[delivery][data][shipmentpointPhone]	string 	Телефон терминала отгрузки
order[delivery][data][shipmentpointCoordinateLatitude]	string 	Координаты терминала отгрузки, широта
order[delivery][data][shipmentpointCoordinateLongitude]	string 	Координаты терминала отгрузки, долгота
order[delivery][data][pickuppointName]	string 	Наименование пункта самовывоза
order[delivery][data][pickuppointCoordinateLatitude]	string 	Координаты ПВЗ, широта
order[delivery][data][pickuppointCoordinateLongitude]	string 	Координаты ПВЗ, долгота
order[delivery][data][extraData]	array 	Дополнительные данные доставки (deliveryDataField.code => значение)
order[delivery][data][itemDeclaredValues][]	array of objects (DeclaredValueItem) 	
order[delivery][data][itemDeclaredValues][][orderProduct]	object (PackageItemOrderProduct) 	Позиция в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][id]	integer 	ID позиции в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][externalId]	string 	deprecated Внешний ID позиции в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][externalIds][]	array of objects (CodeValueModel) 	Внешние идентификаторы позиции в заказе
order[delivery][data][itemDeclaredValues][][orderProduct][externalIds][][code]	string 	Код
order[delivery][data][itemDeclaredValues][][orderProduct][externalIds][][value]	string 	Значение
order[delivery][data][itemDeclaredValues][][value]	double 	Объявленная стоимость товара
order[delivery][data][packages][]	array of objects (Package) 	Упаковки
order[delivery][data][packages][][packageId]	string 	Идентификатор упаковки
order[delivery][data][packages][][weight]	double 	Вес г.
order[delivery][data][packages][][length]	integer 	Длина мм.
order[delivery][data][packages][][width]	integer 	Ширина мм.
order[delivery][data][packages][][height]	integer 	Высота мм.
order[delivery][data][packages][][items][]	array of objects (PackageItem) 	Содержимое упаковки
order[delivery][data][packages][][items][][orderProduct]	object (PackageItemOrderProduct) 	Позиция в заказе
order[delivery][data][packages][][items][][orderProduct][id]	integer 	ID позиции в заказе
order[delivery][data][packages][][items][][orderProduct][externalId]	string 	deprecated Внешний ID позиции в заказе
order[delivery][data][packages][][items][][orderProduct][externalIds][]	array of objects (CodeValueModel) 	Внешние идентификаторы позиции в заказе
order[delivery][data][packages][][items][][orderProduct][externalIds][][code]	string 	Код
order[delivery][data][packages][][items][][orderProduct][externalIds][][value]	string 	Значение
order[delivery][data][packages][][items][][quantity]	double 	Количество товара в упаковке
order[delivery][service]	object (SerializedDeliveryService) 	
order[delivery][service][name]	string 	Название
order[delivery][service][code]	string 	Символьный код
order[delivery][service][active]	boolean 	Статус активности
order[delivery][cost]	double 	Стоимость доставки
order[delivery][netCost]	double 	Себестоимость доставки
order[delivery][date]	DateTime 	Дата доставки
order[delivery][time]	object (TimeInterval) 	Информация о временном диапазоне
order[delivery][time][from]	DateTime 	Время "с"
order[delivery][time][to]	DateTime 	Время "до"
order[delivery][time][custom]	string 	Временной диапазон в свободной форме
order[delivery][address]	object (OrderDeliveryAddress) 	Адрес доставки
order[delivery][address][index]	string 	Индекс
order[delivery][address][countryIso]	string 	ISO код страны
order[delivery][address][region]	string 	Регион
order[delivery][address][regionId]	integer 	Идентификатор региона в Geohelper
order[delivery][address][city]	string 	Город
order[delivery][address][cityId]	integer 	Идентификатор города в Geohelper
order[delivery][address][cityType]	string 	Тип населенного пункта
order[delivery][address][street]	string 	Улица
order[delivery][address][streetId]	integer 	Идентификатор улицы в Geohelper
order[delivery][address][streetType]	string 	Тип улицы
order[delivery][address][building]	string 	Дом
order[delivery][address][flat]	string 	Номер квартиры/офиса
order[delivery][address][floor]	integer 	Этаж
order[delivery][address][block]	integer 	Подъезд
order[delivery][address][house]	string 	Строение
order[delivery][address][housing]	string 	Корпус
order[delivery][address][metro]	string 	Метро
order[delivery][address][notes]	string 	Примечания к адресу
order[delivery][address][text]	string 	Адрес в текстовом виде
order[delivery][vatRate]	string 	Ставка НДС
order[site]	string 	Магазин
order[status]	string 	Статус заказа
order[statusComment]	string 	Комментарий к последнему изменению статуса
order[source]	object (SerializedSource) 	Источник заказа
order[source][source]	string 	Источник
order[source][medium]	string 	Канал
order[source][campaign]	string 	Кампания
order[source][keyword]	string 	Ключевое слово
order[source][content]	string 	Содержание кампании
order[items][]	array of objects (OrderProduct) 	Позиция в заказе
order[items][][externalId]	string 	deprecated Внешний ID позиции в заказе
order[items][][bonusesChargeTotal]	double 	Количество списанных бонусов
order[items][][bonusesCreditTotal]	double 	Количество начисленных бонусов
order[items][][markingCodes][]	array of strings 	deprecated Коды маркировки. Используйте markingObjects
order[items][][markingObjects][]	array of objects (OrderProductMarkingCode) 	Данные маркировки
order[items][][markingObjects][][code]	string 	Значение кода маркировки
order[items][][markingObjects][][provider]	custom handler result for (enum) 	Тип кода маркировки. Возможные значения: chestny_znak, giis_dmdk
order[items][][id]	integer 	ID позиции в заказе
order[items][][externalIds][]	array of objects (CodeValueModel) 	Внешние идентификаторы позиции в заказе
order[items][][externalIds][][code]	string 	Код
order[items][][externalIds][][value]	string 	Значение
order[items][][priceType]	object (PriceType) 	Тип цены
order[items][][priceType][code]	string 	Код типа цены
order[items][][initialPrice]	double 	Цена товара/SKU (в валюте объекта)
order[items][][discounts][]	array of objects (AbstractDiscount) 	Массив скидок
order[items][][discounts][][type]	string 	Тип скидки. Возможные значения:
manual_order - Разовая скидка на заказ;
manual_product - Дополнительная скидка на товар;
loyalty_level - Скидка по уровню программы лояльности;
loyalty_event - Скидка по событию программы лояльности;
personal - Персональная скидка;
bonus_charge - Списание бонусов ПЛ;
round - Скидка от округления
order[items][][discounts][][amount]	float 	Сумма скидки
order[items][][discountTotal]	double 	Итоговая денежная скидка на единицу товара c учетом всех скидок на товар и заказ (в валюте объекта)
order[items][][prices][]	array of objects (OrderProductPriceItem) 	Набор итоговых цен реализации с указанием количества
order[items][][prices][][price]	float 	Итоговая цена c учетом всех скидок на товар и заказ (в валюте объекта)
order[items][][prices][][quantity]	float 	Количество товара по заданной цене
order[items][][vatRate]	string 	Ставка НДС
order[items][][createdAt]	DateTime 	Дата создания позиции в системе
order[items][][quantity]	float 	Количество
order[items][][status]	string 	Статус позиции в заказе
order[items][][comment]	string 	Комментарий к позиции в заказе
order[items][][offer]	object (Offer) 	Торговое предложение
order[items][][offer][displayName]	string 	Название SKU
order[items][][offer][id]	integer 	ID торгового предложения
order[items][][offer][externalId]	string 	ID торгового предложения в магазине
order[items][][offer][xmlId]	string 	ID торгового предложения в складской системе
order[items][][offer][name]	string 	Название
order[items][][offer][article]	string 	Артикул
order[items][][offer][vatRate]	string 	Ставка НДС
order[items][][offer][properties]	array 	Свойства SKU
order[items][][offer][unit]	object (Unit) 	Единица измерения
order[items][][offer][unit][code]	string 	Символьный код
order[items][][offer][unit][name]	string 	Название
order[items][][offer][unit][sym]	string 	Краткое обозначение
order[items][][offer][barcode]	string 	Штрих-код
order[items][][isCanceled]	boolean 	Данная позиция в заказе является отменной
order[items][][properties]	array 	[массив] Дополнительные свойства позиции в заказе
order[items][][purchasePrice]	double 	Закупочная цена (в базовой валюте)
order[items][][ordering]	integer 	Порядок
order[fullPaidAt]	DateTime 	Дата полной оплаты
order[payments][]	array of objects (Payment) 	Платежи
order[payments][][id]	integer 	Внутренний ID
order[payments][][status]	string 	Статус оплаты
order[payments][][type]	string 	Тип оплаты
order[payments][][externalId]	string 	Внешний ID платежа
order[payments][][amount]	double 	Сумма платежа (в валюте объекта)
order[payments][][paidAt]	DateTime 	Дата оплаты
order[payments][][comment]	string 	Комментарий
order[fromApi]	boolean 	Заказ поступил через API
order[weight]	double 	Вес
order[length]	integer 	Длина
order[width]	integer 	Ширина
order[height]	integer 	Высота
order[shipmentStore]	string 	Склад отгрузки
order[shipmentDate]	DateTime 	Дата отгрузки
order[shipped]	boolean 	Заказ отгружен
order[links][]	array of objects (OrderLink) 	Связь заказов
order[links][][order]	object (LinkedOrder) 	Связанный заказ
order[links][][order][id]	integer 	ID связанного заказа
order[links][][order][number]	string 	Номер связанного заказа
order[links][][order][externalId]	string 	Внешний ID связанного заказа
order[links][][createdAt]	DateTime 	Дата/время создания связи с заказом
order[links][][comment]	string 	Комментарий
order[customFields]	array 	Ассоциативный массив пользовательских полей
order[clientId]	string 	Метка клиента Google Analytics
Статусы ответа
Код статуса ответа	Описание
201	

    Заказ создан

400	

    Ошибка при создании заказа