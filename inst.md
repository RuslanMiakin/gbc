
https://YOUR_SUBDOMAIN.retailcrm.ru/api/v5/orders


Получение списка заказов, удовлетворяющих заданному фильтру

Для доступа к методу необходимо разрешение order_read.

Результат возвращается постранично. В поле pagination содержится информация о постраничной разбивке.

В фильтрах filter[managers][], filter[couriers][] указывается массив внутренних ID элементов в системе.

В фильтрах filter[orderTypes][], filter[paymentStatuses][], filter[paymentTypes][], filter[deliveryTypes][], filter[orderMethods][], filter[managerGroups][] указывается массив символьных кодов элементов.

В фильтрах filter[sourceName], filter[mediumName], filter[campaignName], filter[keywordName], filter[adContentName] указывается название элементов.

В фильтре filter[numbers] производится точное сравнение с элементами заданного массива строк.

В фильтрах filter[ids][] и filter[externalIds][] передается массив внутренних и внешних идентификаторов соответственно.

В фильтре filter[extendedStatus][] можно указывать один или несколько статусов или групп статусов заказа. Для фильтрации по статусу передается символьный код статуса. Для фильтрации по группе статусов передается символьный код группы статусов плюс постфикс -group. Пример: filter[extendedStatus][]=new&filter[extendedStatus][]=approval-group.

С помощью фильтра filter[customFields][] можно производить поиск по значениям пользовательских полей. Для полей типа «Справочник» указывается символьный код значения в справочнике. Для полей типа «Дата» и «Дата-время» указывается дата в формате Y-m-d. Для других типов полей указывается непосредственно значение.

Для пользовательских полей типа Целое число, Число, Дата и Дата-время фильтрация осуществляется по диапазону, для остальных типов полей — по точному значению. Имя фильтра соответствует символьному коду поля. Пример: для поля типа Дата с символьным кодом birth_date доступны фильтры filter[customFields][birth_date][min] и filter[customFields][birth_date][max]. Для поля типа Справочник с символьным кодом quality доступен множественный фильтр filter[customFields][quality][].

В фильтре filter[attachments] можно указать одно из трех значений:

    1 - возвращает заказы, у которых есть прикрепленные файлы и вложения к письмам;
    2 - возвращает заказы, у которых есть прикрепленные файлы;
    3 - возвращает заказы, у которых нет прикрепленных файлов.

В фильтре filter[tasksCounts] можно указать одно из трех значений:

    1 - возвращает заказы, у которых нет невыполненных задач;
    2 - возвращает заказы, у которых есть какие-либо невыполненные задачи (как просроченные, так и не просроченные);
    3 - возвращает только те заказы, у которых среди невыполненных задач есть просроченные.

В фильтре filter[mgChannels] указывается массив внутренних ID каналов в системе. Фильтр выбирает заказы созданные через правый виджет чатов.

Пустые поля без значений не возвращаются.

В полях orderType, orderMethod, payments[][type], payments[][status], status, site, delivery[code] возвращается символьный код элемента.

В полях managerId, sourceId возвращается внутренний ID сущности в системе.

В поле customFields возвращается массив значений пользовательских полей. Для полей типа «Справочник» указывается символьный код значения в справочнике. Для полей типа «Дата» указывается дата в формате Y-m-d. Для других типов полей указывается непосредственно значение.

Если адрес доставки указывался в строковом виде, то он будет возвращен в delivery[address][text]. Если адрес указывался в детальном виде, то будут возвращены все заполненные поля доставки, а в delivery[address][text] будет находиться автоматически сформированное текстовое представление адреса.

Поле privilegeType может содержать одно из следующих значений:

    personal_discount - персональная скидка клиента;
    loyalty_level - расчет скидки или начисление бонусов исходя из настроек уровня ПЛ клиента;
    loyalty_event - расчет скидки по событию ПЛ;
    none - не применять скидки ПЛ к заказу;

Параметры
Параметр	Тип	Формат	Описание
limit	integer	{not blank}[20|50|100]}	Количество элементов в ответе (по умолчанию равно 20)
page	integer	{not blank}{range: {>=1}}}	Номер страницы с результатами (по умолчанию равно 1)
filter	object (OrderFilterData)		
filter[ids][]	array of integers		Массив ID заказов
filter[externalIds][]	array of strings		Массив externalID заказов
filter[numbers][]	array of strings		Массив номеров заказов (не более 100 номеров в одном запросе)
filter[customerId]	integer	{range: {>=0, <=100000000000}}	Внутренний ID клиента
filter[customerExternalId]	string	{length: {max: 255}}	Внешний ID клиента
filter[customer]	string	{length: {max: 255}}	Клиент (ФИО или телефон)
filter[customerType]	string	[customer|customer_corporate]	Тип клиента
filter[email]	string	{length: {max: 255}}	E-mail
filter[managers][]	array of integers		Менеджеры
filter[managerGroups][]	array of strings		Группы менеджеров
filter[paymentStatuses][]	array of strings		Статусы оплаты
filter[orderTypes][]	array of strings		Типы заказа
filter[orderMethods][]	array of strings		Способы оформления
filter[product]	string	{length: {max: 255}}	Товар (название или артикул)
filter[productSearchType]	string		
filter[extendedStatus][]	array of strings		Статус заказа
filter[statusHistorySequence][]	array of strings		
filter[statusComment]	string	{length: {max: 255}}	
filter[sites][]	array of strings		Магазины
filter[vip]	boolean		Важный клиент
filter[bad]	boolean		Плохой клиент
filter[expired]	boolean		Заказ просрочен
filter[call]	boolean		Требуется позвонить
filter[online]	boolean		Клиент на сайте
filter[paymentTypes][]	array of strings		Типы оплаты
filter[deliveryStates][]	array of strings	{choice of [cancel|cancel_force|error|none|processing|success]}	Статусы оформления
filter[deliveryTypes][]	array of strings		Типы доставки
filter[deliveryServices][]	array of strings		Службы доставки
filter[countries][]	array of strings		Страны
filter[region]	string	{length: {max: 255}}	Регион
filter[city]	string	{length: {max: 255}}	Город
filter[index]	string		Почтовый индекс
filter[metro]	string	{length: {max: 255}}	Метро
filter[sourceName]	string	{length: {max: 255}}	Источник
filter[mediumName]	string	{length: {max: 255}}	Канал
filter[campaignName]	string	{length: {max: 255}}	Кампания
filter[keywordName]	string		Ключевое слово
filter[adContentName]	string		Содержание кампании
filter[managerComment]	string	{length: {max: 255}}	Комментарий менеджера
filter[customerComment]	string	{length: {max: 255}}	Комментарий клиента
filter[trackNumber]	string	{length: {max: 255}}	Номер отправления в службе доставки
filter[deliveryExternalId]	string		Идентификатор в службе доставки
filter[couriers][]	array of integers		Курьеры
filter[contragentName]	string	{length: {max: 255}}	Полное наименование
filter[contragentTypes][]	array of strings	{choice of [enterpreneur|individual|legal-entity]}	Типы контрагента
filter[contragentInn]	string	{match: /\d+/}	ИНН
filter[contragentKpp]	string	{match: /\d+/}	КПП
filter[contragentBik]	string	{match: /\d+/}	БИК банка
filter[contragentCorrAccount]	string	{match: /\d+/}	Корр. счет банка
filter[contragentBankAccount]	string	{match: /\d+/}	Расчетный счет
filter[companyName]	string	{length: {max: 255}}	Компания (название)
filter[deliveryAddressNotes]	string	{length: {max: 255}}	Примечания к адресу доставки
filter[productGroups][]	array of integers		
filter[shipmentStores][]	array of strings		Склады отгрузки
filter[shipped]	boolean		Отгружен
filter[attachments]	integer	[1|2|3]	Прикрепленные объекты (вложения)
filter[receiptFiscalDocumentAttribute]	string	{length: {max: 255}}	Фискальный признак документа
filter[receiptStatus]	string	[done|fail|wait]	Статус фискализации
filter[receiptOperation]	string	[sell|sell_refund]	Операция фискализации
filter[receiptOrderStatus]	string	[done|fail|wait]	Статус полной фискализации
filter[mgChannels][]	array of integers		Каналы чатов
filter[tasksCounts]	integer	[1|2|3]	Задачи
filter[tags][]	array of strings		
filter[attachedTags][]	array of strings		
filter[createdAtFrom]	DateTime	Y-m-d	Дата оформления заказа (от)
filter[createdAtTo]	DateTime	Y-m-d	Дата оформления заказа (до)
filter[fullPaidAtFrom]	DateTime	Y-m-d	Дата полной оплаты (от)
filter[fullPaidAtTo]	DateTime	Y-m-d	Дата полной оплаты (до)
filter[deliveryDateFrom]	DateTime	Y-m-d	Дата доставки (от)
filter[deliveryDateTo]	DateTime	Y-m-d	Дата доставки (до)
filter[statusUpdatedAtFrom]	DateTime	Y-m-d	Дата последнего изменения статуса (от)
filter[statusUpdatedAtTo]	DateTime	Y-m-d	Дата последнего изменения статуса (до)
filter[shipmentDateFrom]	DateTime	Y-m-d	Дата отгрузки (от)
filter[shipmentDateTo]	DateTime	Y-m-d	Дата отгрузки (до)
filter[firstWebVisitFrom]	DateTime	Y-m-d	Первое посещение (от)
filter[firstWebVisitTo]	DateTime	Y-m-d	Первое посещение (до)
filter[lastWebVisitFrom]	DateTime	Y-m-d	Последнее посещение (от)
filter[lastWebVisitTo]	DateTime	Y-m-d	Последнее посещение (до)
filter[firstOrderFrom]	DateTime	Y-m-d	Первый заказ (от)
filter[firstOrderTo]	DateTime	Y-m-d	Первый заказ (до)
filter[lastOrderFrom]	DateTime	Y-m-d	Последний заказ (от)
filter[lastOrderTo]	DateTime	Y-m-d	Последний заказ (до)
filter[paidAtFrom]	DateTime	Y-m-d	Дата оплаты (от)
filter[paidAtTo]	DateTime	Y-m-d	Дата оплаты (до)
filter[deliveryTimeFrom]	DateTime	HH:MM:SS	Время доставки (с)
filter[deliveryTimeTo]	DateTime	HH:MM:SS	Время доставки (до)
filter[minPrice]	integer		Стоимость заказа (от)
filter[maxPrice]	integer		Стоимость заказа (до)
filter[minCostSumm]	integer		Сумма расходов (от)
filter[maxCostSumm]	integer		Сумма расходов (до)
filter[minPrepaySumm]	integer		Оплачено (от)
filter[maxPrepaySumm]	integer		Оплачено (до)
filter[minDeliveryCost]	integer		Стоимость доставки (от)
filter[maxDeliveryCost]	integer		Стоимость доставки (до)
filter[minDeliveryNetCost]	integer		Себестоимость доставки (от)
filter[maxDeliveryNetCost]	integer		Себестоимость доставки (до)
filter[minMarginSumm]	integer		Валовая прибыль заказа (от)
filter[maxMarginSumm]	integer		Валовая прибыль заказа (до)
filter[minPurchaseSumm]	integer		Закупочная стоимость заказа (от)
filter[maxPurchaseSumm]	integer		Закупочная стоимость заказа (до)
filter[customFields]	array		Фильтр по пользовательским полям
Ответ
Параметр	Тип	Описание
success	boolean 	Результат запроса (успешный/неуспешный)
pagination	object (PaginationResponse) 	Постраничная разбивка
pagination[limit]	integer 	Количество элементов в ответе
pagination[totalCount]	integer 	Общее количество найденных элементов
pagination[currentPage]	integer 	Текущая страница выдачи
pagination[totalPageCount]	integer 	Общее количество страниц выдачи
orders[]	array of objects (Order) 	Заказ
orders[][slug]	custom handler result for (int) 	deprecated Символьный код
orders[][bonusesCreditTotal]	double 	Количество начисленных бонусов
orders[][bonusesChargeTotal]	double 	Количество списанных бонусов
orders[][summ]	double 	Сумма по товарам/услугам (в валюте объекта)
orders[][currency]	string 	Валюта
orders[][id]	integer 	ID заказа
orders[][number]	string 	Номер заказа
orders[][externalId]	string 	Внешний ID заказа
orders[][orderType]	string 	Тип заказа
orders[][orderMethod]	string 	Способ оформления
orders[][privilegeType]	string 	Тип привилегии
orders[][countryIso]	string 	ISO код страны
orders[][createdAt]	DateTime 	Дата оформления заказа
orders[][statusUpdatedAt]	DateTime 	Дата последнего изменения статуса
orders[][totalSumm]	double 	Общая сумма с учетом скидки (в валюте объекта)
orders[][prepaySum]	double 	Оплаченная сумма (в валюте объекта)
orders[][purchaseSumm]	double 	Общая стоимость закупки (в базовой валюте)
orders[][personalDiscountPercent]	double 	Персональная скидка на заказ
orders[][loyaltyLevel]	object (LoyaltyLevel) 	Уровень участия в программе лояльности
orders[][loyaltyLevel][id]	integer 	ID уровня
orders[][loyaltyLevel][name]	string 	Название уровня
orders[][loyaltyEventDiscount]	object (LoyaltyEventDiscount) 	Скидка по событию программы лояльности
orders[][loyaltyEventDiscount][id]	integer 	ID
orders[][mark]	integer 	Оценка заказа
orders[][markDatetime]	DateTime 	Дата и время получение оценки от покупателя
orders[][lastName]	string 	Фамилия
orders[][firstName]	string 	Имя
orders[][patronymic]	string 	Отчество
orders[][phone]	string 	Телефон
orders[][additionalPhone]	string 	Дополнительный телефон
orders[][email]	string 	E-mail
orders[][call]	boolean 	Требуется позвонить
orders[][expired]	boolean 	Просрочен
orders[][customerComment]	string 	Комментарий клиента
orders[][managerComment]	string 	Комментарий оператора
orders[][managerId]	integer 	Менеджер, прикрепленный к заказу
orders[][customer]		Клиент
orders[][customer][type]	string 	Тип клиента
orders[][customer][id]	integer 	ID клиента
orders[][customer][externalId]	string 	Внешний ID клиента
orders[][customer][isContact]	boolean 	Клиент является контактным лицом (создан как контактное лицо и на него нет оформленных заказов)
orders[][customer][createdAt]	DateTime 	Создан
orders[][customer][managerId]	integer 	Менеджер клиента
orders[][customer][vip]	boolean 	Важный клиент
orders[][customer][bad]	boolean 	Плохой клиент
orders[][customer][site]	string 	Магазин, с которого пришел клиент
orders[][customer][contragent]	object (CustomerContragent) 	deprecated Реквизиты (Поля объекта следует использовать только при неактивированной функциональности "Корпоративные клиенты")
orders[][customer][contragent][contragentType]	string 	Тип контрагента
orders[][customer][contragent][legalName]	string 	Полное наименование
orders[][customer][contragent][legalAddress]	string 	Адрес регистрации
orders[][customer][contragent][INN]	string 	ИНН
orders[][customer][contragent][OKPO]	string 	ОКПО
orders[][customer][contragent][KPP]	string 	КПП
orders[][customer][contragent][OGRN]	string 	ОГРН
orders[][customer][contragent][OGRNIP]	string 	ОГРНИП
orders[][customer][contragent][certificateNumber]	string 	Номер свидетельства
orders[][customer][contragent][certificateDate]	DateTime 	Дата свидетельства
orders[][customer][contragent][BIK]	string 	БИК
orders[][customer][contragent][bank]	string 	Банк
orders[][customer][contragent][bankAddress]	string 	Адрес банка
orders[][customer][contragent][corrAccount]	string 	Корр. счёт
orders[][customer][contragent][bankAccount]	string 	Расчётный счёт
orders[][customer][tags][]	array of objects (CustomerTagLink) 	[массив] Теги
orders[][customer][tags][][color]	string 	
orders[][customer][tags][][name]	string 	
orders[][customer][tags][][colorCode]	string 	
orders[][customer][tags][][attached]	boolean 	
orders[][customer][firstClientId]	string 	Первая метка клиента Google Analytics
orders[][customer][lastClientId]	string 	Последняя метка клиента Google Analytics
orders[][customer][customFields]	array 	Ассоциативный массив пользовательских полей
orders[][customer][personalDiscount]	double 	Персональная скидка
orders[][customer][cumulativeDiscount]	double 	deprecated Накопительная скидка (Недоступно, начиная с 8 версии системы)
orders[][customer][discountCardNumber]	string 	Номер дисконтной карты
orders[][customer][avgMarginSumm]	float 	Средняя валовая прибыль по заказам клиента (в базовой валюте)
orders[][customer][marginSumm]	float 	LTV (в базовой валюте)
orders[][customer][totalSumm]	float 	Общая сумма заказов (в базовой валюте)
orders[][customer][averageSumm]	float 	Средняя сумма заказа (в базовой валюте)
orders[][customer][ordersCount]	integer 	Количество заказов
orders[][customer][costSumm]	float 	Сумма расходов (в базовой валюте)
orders[][customer][address]	object (CustomerAddress) 	Адрес клиента
orders[][customer][address][id]	integer 	ID адреса
orders[][customer][address][index]	string 	Индекс
orders[][customer][address][countryIso]	string 	ISO код страны
orders[][customer][address][region]	string 	Регион
orders[][customer][address][regionId]	integer 	Идентификатор региона в Geohelper
orders[][customer][address][city]	string 	Город
orders[][customer][address][cityId]	integer 	Идентификатор города в Geohelper
orders[][customer][address][cityType]	string 	Тип населенного пункта
orders[][customer][address][street]	string 	Улица
orders[][customer][address][streetId]	integer 	Идентификатор улицы в Geohelper
orders[][customer][address][streetType]	string 	Тип улицы
orders[][customer][address][building]	string 	Дом
orders[][customer][address][flat]	string 	Номер квартиры/офиса
orders[][customer][address][floor]	integer 	Этаж
orders[][customer][address][block]	integer 	Подъезд
orders[][customer][address][house]	string 	Строение
orders[][customer][address][housing]	string 	Корпус
orders[][customer][address][metro]	string 	Метро
orders[][customer][address][notes]	string 	Примечания к адресу
orders[][customer][address][text]	string 	Адрес в текстовом виде
orders[][customer][address][externalId]	string 	Внешний ID
orders[][customer][address][name]	string 	Наменование адреса
orders[][customer][segments][]	array of objects (Segment) 	[массив] Сегменты
orders[][customer][segments][][id]	integer 	Внутренний ID сегмента
orders[][customer][segments][][code]	string 	Символьный код
orders[][customer][segments][][name]	string 	Название сегмента
orders[][customer][segments][][createdAt]	DateTime 	Дата создания сегмента
orders[][customer][segments][][isDynamic]	boolean 	Является ли сегмент автоматически пересчитываемым
orders[][customer][segments][][customersCount]	integer 	Количество клиентов в сегменте
orders[][customer][segments][][active]	boolean 	Активность сегмента
orders[][customer][maturationTime]	integer 	Время «созревания», в секундах
orders[][customer][firstName]	string 	Имя
orders[][customer][lastName]	string 	Фамилия
orders[][customer][patronymic]	string 	Отчество
orders[][customer][sex]	string 	Пол
orders[][customer][presumableSex]	string 	Предполагаемый пол на основе ФИО
orders[][customer][email]	string 	E-mail
orders[][customer][emailMarketingUnsubscribedAt]	DateTime 	deprecated Дата отписки от email рассылок
orders[][customer][customerSubscriptions][]	array of objects (CustomerSubscription) 	Подписки
orders[][customer][customerSubscriptions][][subscription]	object (Subscription) 	Категория подписки
orders[][customer][customerSubscriptions][][subscription][id]	integer 	ID категории подписки
orders[][customer][customerSubscriptions][][subscription][channel]	string 	Канал
orders[][customer][customerSubscriptions][][subscription][name]	string 	Название
orders[][customer][customerSubscriptions][][subscription][code]	string 	Символьный код
orders[][customer][customerSubscriptions][][subscription][active]	boolean 	Статус активности
orders[][customer][customerSubscriptions][][subscription][autoSubscribe]	boolean 	Автоматически подписывать новых клиентов
orders[][customer][customerSubscriptions][][subscription][ordering]	integer 	Порядок
orders[][customer][customerSubscriptions][][subscribed]	boolean 	Активность подписки
orders[][customer][customerSubscriptions][][changedAt]	DateTime 	Дата изменения флага активности
orders[][customer][phones][]	array of objects (CustomerPhone) 	Телефоны
orders[][customer][phones][][number]	string 	Номер телефона
orders[][customer][birthday]	DateTime 	День рождения
orders[][customer][source]	object (SerializedSource) 	Источник клиента
orders[][customer][source][source]	string 	Источник
orders[][customer][source][medium]	string 	Канал
orders[][customer][source][campaign]	string 	Кампания
orders[][customer][source][keyword]	string 	Ключевое слово
orders[][customer][source][content]	string 	Содержание кампании
orders[][customer][mgCustomers][]	array of objects (MGCustomer) 	Клиенты MessageGateway
orders[][customer][mgCustomers][][id]	integer 	ID клиента
orders[][customer][mgCustomers][][externalId]	integer 	Внешний ID MessageGateway клиента
orders[][customer][mgCustomers][][mgChannel]	object (MGChannel) 	MessageGateway канал
orders[][customer][mgCustomers][][mgChannel][allowedSendByPhone]	custom handler result for (bool) 	Можно ли писать первыми в этот канал по номеру телефона
orders[][customer][mgCustomers][][mgChannel][id]	integer 	ID канала
orders[][customer][mgCustomers][][mgChannel][externalId]	integer 	Внешний ID канала
orders[][customer][mgCustomers][][mgChannel][type]	string 	Тип канала
orders[][customer][mgCustomers][][mgChannel][active]	boolean 	Активность канала
orders[][customer][mgCustomers][][mgChannel][name]	string 	Название канала
orders[][customer][photoUrl]	string 	URL фотографии
orders[][contact]	object (Customer) 	Контактное лицо
orders[][contact][type]	string 	Тип клиента
orders[][contact][id]	integer 	ID клиента
orders[][contact][externalId]	string 	Внешний ID клиента
orders[][contact][isContact]	boolean 	Клиент является контактным лицом (создан как контактное лицо и на него нет оформленных заказов)
orders[][contact][createdAt]	DateTime 	Создан
orders[][contact][managerId]	integer 	Менеджер клиента
orders[][contact][vip]	boolean 	Важный клиент
orders[][contact][bad]	boolean 	Плохой клиент
orders[][contact][site]	string 	Магазин, с которого пришел клиент
orders[][contact][contragent]	object (CustomerContragent) 	deprecated Реквизиты (Поля объекта следует использовать только при неактивированной функциональности "Корпоративные клиенты")
orders[][contact][contragent][contragentType]	string 	Тип контрагента
orders[][contact][contragent][legalName]	string 	Полное наименование
orders[][contact][contragent][legalAddress]	string 	Адрес регистрации
orders[][contact][contragent][INN]	string 	ИНН
orders[][contact][contragent][OKPO]	string 	ОКПО
orders[][contact][contragent][KPP]	string 	КПП
orders[][contact][contragent][OGRN]	string 	ОГРН
orders[][contact][contragent][OGRNIP]	string 	ОГРНИП
orders[][contact][contragent][certificateNumber]	string 	Номер свидетельства
orders[][contact][contragent][certificateDate]	DateTime 	Дата свидетельства
orders[][contact][contragent][BIK]	string 	БИК
orders[][contact][contragent][bank]	string 	Банк
orders[][contact][contragent][bankAddress]	string 	Адрес банка
orders[][contact][contragent][corrAccount]	string 	Корр. счёт
orders[][contact][contragent][bankAccount]	string 	Расчётный счёт
orders[][contact][tags][]	array of objects (CustomerTagLink) 	[массив] Теги
orders[][contact][tags][][color]	string 	
orders[][contact][tags][][name]	string 	
orders[][contact][tags][][colorCode]	string 	
orders[][contact][tags][][attached]	boolean 	
orders[][contact][firstClientId]	string 	Первая метка клиента Google Analytics
orders[][contact][lastClientId]	string 	Последняя метка клиента Google Analytics
orders[][contact][customFields]	array 	Ассоциативный массив пользовательских полей
orders[][contact][personalDiscount]	double 	Персональная скидка
orders[][contact][cumulativeDiscount]	double 	deprecated Накопительная скидка (Недоступно, начиная с 8 версии системы)
orders[][contact][discountCardNumber]	string 	Номер дисконтной карты
orders[][contact][avgMarginSumm]	float 	Средняя валовая прибыль по заказам клиента (в базовой валюте)
orders[][contact][marginSumm]	float 	LTV (в базовой валюте)
orders[][contact][totalSumm]	float 	Общая сумма заказов (в базовой валюте)
orders[][contact][averageSumm]	float 	Средняя сумма заказа (в базовой валюте)
orders[][contact][ordersCount]	integer 	Количество заказов
orders[][contact][costSumm]	float 	Сумма расходов (в базовой валюте)
orders[][contact][address]	object (CustomerAddress) 	Адрес клиента
orders[][contact][address][id]	integer 	ID адреса
orders[][contact][address][index]	string 	Индекс
orders[][contact][address][countryIso]	string 	ISO код страны
orders[][contact][address][region]	string 	Регион
orders[][contact][address][regionId]	integer 	Идентификатор региона в Geohelper
orders[][contact][address][city]	string 	Город
orders[][contact][address][cityId]	integer 	Идентификатор города в Geohelper
orders[][contact][address][cityType]	string 	Тип населенного пункта
orders[][contact][address][street]	string 	Улица
orders[][contact][address][streetId]	integer 	Идентификатор улицы в Geohelper
orders[][contact][address][streetType]	string 	Тип улицы
orders[][contact][address][building]	string 	Дом
orders[][contact][address][flat]	string 	Номер квартиры/офиса
orders[][contact][address][floor]	integer 	Этаж
orders[][contact][address][block]	integer 	Подъезд
orders[][contact][address][house]	string 	Строение
orders[][contact][address][housing]	string 	Корпус
orders[][contact][address][metro]	string 	Метро
orders[][contact][address][notes]	string 	Примечания к адресу
orders[][contact][address][text]	string 	Адрес в текстовом виде
orders[][contact][address][externalId]	string 	Внешний ID
orders[][contact][address][name]	string 	Наменование адреса
orders[][contact][segments][]	array of objects (Segment) 	[массив] Сегменты
orders[][contact][segments][][id]	integer 	Внутренний ID сегмента
orders[][contact][segments][][code]	string 	Символьный код
orders[][contact][segments][][name]	string 	Название сегмента
orders[][contact][segments][][createdAt]	DateTime 	Дата создания сегмента
orders[][contact][segments][][isDynamic]	boolean 	Является ли сегмент автоматически пересчитываемым
orders[][contact][segments][][customersCount]	integer 	Количество клиентов в сегменте
orders[][contact][segments][][active]	boolean 	Активность сегмента
orders[][contact][maturationTime]	integer 	Время «созревания», в секундах
orders[][contact][firstName]	string 	Имя
orders[][contact][lastName]	string 	Фамилия
orders[][contact][patronymic]	string 	Отчество
orders[][contact][sex]	string 	Пол
orders[][contact][presumableSex]	string 	Предполагаемый пол на основе ФИО
orders[][contact][email]	string 	E-mail
orders[][contact][emailMarketingUnsubscribedAt]	DateTime 	deprecated Дата отписки от email рассылок
orders[][contact][customerSubscriptions][]	array of objects (CustomerSubscription) 	Подписки
orders[][contact][customerSubscriptions][][subscription]	object (Subscription) 	Категория подписки
orders[][contact][customerSubscriptions][][subscription][id]	integer 	ID категории подписки
orders[][contact][customerSubscriptions][][subscription][channel]	string 	Канал
orders[][contact][customerSubscriptions][][subscription][name]	string 	Название
orders[][contact][customerSubscriptions][][subscription][code]	string 	Символьный код
orders[][contact][customerSubscriptions][][subscription][active]	boolean 	Статус активности
orders[][contact][customerSubscriptions][][subscription][autoSubscribe]	boolean 	Автоматически подписывать новых клиентов
orders[][contact][customerSubscriptions][][subscription][ordering]	integer 	Порядок
orders[][contact][customerSubscriptions][][subscribed]	boolean 	Активность подписки
orders[][contact][customerSubscriptions][][changedAt]	DateTime 	Дата изменения флага активности
orders[][contact][phones][]	array of objects (CustomerPhone) 	Телефоны
orders[][contact][phones][][number]	string 	Номер телефона
orders[][contact][birthday]	DateTime 	День рождения
orders[][contact][source]	object (SerializedSource) 	Источник клиента
orders[][contact][source][source]	string 	Источник
orders[][contact][source][medium]	string 	Канал
orders[][contact][source][campaign]	string 	Кампания
orders[][contact][source][keyword]	string 	Ключевое слово
orders[][contact][source][content]	string 	Содержание кампании
orders[][contact][mgCustomers][]	array of objects (MGCustomer) 	Клиенты MessageGateway
orders[][contact][mgCustomers][][id]	integer 	ID клиента
orders[][contact][mgCustomers][][externalId]	integer 	Внешний ID MessageGateway клиента
orders[][contact][mgCustomers][][mgChannel]	object (MGChannel) 	MessageGateway канал
orders[][contact][mgCustomers][][mgChannel][allowedSendByPhone]	custom handler result for (bool) 	Можно ли писать первыми в этот канал по номеру телефона
orders[][contact][mgCustomers][][mgChannel][id]	integer 	ID канала
orders[][contact][mgCustomers][][mgChannel][externalId]	integer 	Внешний ID канала
orders[][contact][mgCustomers][][mgChannel][type]	string 	Тип канала
orders[][contact][mgCustomers][][mgChannel][active]	boolean 	Активность канала
orders[][contact][mgCustomers][][mgChannel][name]	string 	Название канала
orders[][contact][photoUrl]	string 	URL фотографии
orders[][company]	object (Company) 	Компания
orders[][company][id]	integer 	ID компании
orders[][company][externalId]	string 	Внешний ID компании
orders[][company][customer]	object (SerializedEntityCustomer) 	Клиент
orders[][company][customer][site]	string 	Символьный код магазина
orders[][company][customer][id]	integer 	Внутренний ID клиента
orders[][company][customer][externalId]	string 	Внешний ID клиента
orders[][company][customer][type]	string 	Тип клиента
orders[][company][active]	boolean 	Активность
orders[][company][name]	string 	Наименование
orders[][company][brand]	string 	Бренд
orders[][company][site]	string 	Сайт компании
orders[][company][createdAt]	DateTime 	Дата создания
orders[][company][contragent]	object (CompanyContragent) 	Реквизиты
orders[][company][contragent][contragentType]	string 	Тип контрагента
orders[][company][contragent][legalName]	string 	Полное наименование
orders[][company][contragent][legalAddress]	string 	Адрес регистрации
orders[][company][contragent][INN]	string 	ИНН
orders[][company][contragent][OKPO]	string 	ОКПО
orders[][company][contragent][KPP]	string 	КПП
orders[][company][contragent][OGRN]	string 	ОГРН
orders[][company][contragent][OGRNIP]	string 	ОГРНИП
orders[][company][contragent][certificateNumber]	string 	Номер свидетельства
orders[][company][contragent][certificateDate]	DateTime 	Дата свидетельства
orders[][company][contragent][BIK]	string 	БИК
orders[][company][contragent][bank]	string 	Банк
orders[][company][contragent][bankAddress]	string 	Адрес банка
orders[][company][contragent][corrAccount]	string 	Корр. счёт
orders[][company][contragent][bankAccount]	string 	Расчётный счёт
orders[][company][address]	object (CustomerAddress) 	Адрес
orders[][company][address][id]	integer 	ID адреса
orders[][company][address][index]	string 	Индекс
orders[][company][address][countryIso]	string 	ISO код страны
orders[][company][address][region]	string 	Регион
orders[][company][address][regionId]	integer 	Идентификатор региона в Geohelper
orders[][company][address][city]	string 	Город
orders[][company][address][cityId]	integer 	Идентификатор города в Geohelper
orders[][company][address][cityType]	string 	Тип населенного пункта
orders[][company][address][street]	string 	Улица
orders[][company][address][streetId]	integer 	Идентификатор улицы в Geohelper
orders[][company][address][streetType]	string 	Тип улицы
orders[][company][address][building]	string 	Дом
orders[][company][address][flat]	string 	Номер квартиры/офиса
orders[][company][address][floor]	integer 	Этаж
orders[][company][address][block]	integer 	Подъезд
orders[][company][address][house]	string 	Строение
orders[][company][address][housing]	string 	Корпус
orders[][company][address][metro]	string 	Метро
orders[][company][address][notes]	string 	Примечания к адресу
orders[][company][address][text]	string 	Адрес в текстовом виде
orders[][company][address][externalId]	string 	Внешний ID
orders[][company][address][name]	string 	Наменование адреса
orders[][company][avgMarginSumm]	float 	Средняя валовая прибыль по заказам клиента (в базовой валюте)
orders[][company][marginSumm]	float 	LTV (в базовой валюте)
orders[][company][totalSumm]	float 	Общая сумма заказов (в базовой валюте)
orders[][company][averageSumm]	float 	Средняя сумма заказа (в базовой валюте)
orders[][company][costSumm]	float 	Сумма расходов (в базовой валюте)
orders[][company][ordersCount]	integer 	Количество заказов
orders[][company][customFields]	array 	Ассоциативный массив пользовательских полей
orders[][contragent]	object (OrderContragent) 	Реквизиты
orders[][contragent][contragentType]	string 	Тип контрагента
orders[][contragent][legalName]	string 	Полное наименование
orders[][contragent][legalAddress]	string 	Адрес регистрации
orders[][contragent][INN]	string 	ИНН
orders[][contragent][OKPO]	string 	ОКПО
orders[][contragent][KPP]	string 	КПП
orders[][contragent][OGRN]	string 	ОГРН
orders[][contragent][OGRNIP]	string 	ОГРНИП
orders[][contragent][certificateNumber]	string 	Номер свидетельства
orders[][contragent][certificateDate]	DateTime 	Дата свидетельства
orders[][contragent][BIK]	string 	БИК
orders[][contragent][bank]	string 	Банк
orders[][contragent][bankAddress]	string 	Адрес банка
orders[][contragent][corrAccount]	string 	Корр. счёт
orders[][contragent][bankAccount]	string 	Расчётный счёт
orders[][delivery]	object (SerializedOrderDelivery) 	Данные о доставке
orders[][delivery][code]	string 	Код типа доставки
orders[][delivery][integrationCode]	string 	Интеграционный код типа доставки
orders[][delivery][data]		Данные службы доставки, подключенной через API
orders[][delivery][data][externalId]	string 	Идентификатор в службе доставки
orders[][delivery][data][trackNumber]	string 	Номер отправления (поле deprecated на запись)
orders[][delivery][data][status]	string 	Код статуса доставки
orders[][delivery][data][locked]	boolean 	Не синхронизировать со службой доставки
orders[][delivery][data][pickuppointAddress]	string 	Адрес пункта самовывоза
orders[][delivery][data][days]	string 	Ориентировочный срок доставки
orders[][delivery][data][statusText]	string 	Наименование статуса доставки
orders[][delivery][data][statusDate]	DateTime 	Дата статуса доставки
orders[][delivery][data][tariff]	string 	Код тарифа
orders[][delivery][data][tariffName]	string 	Наименование тарифа
orders[][delivery][data][pickuppointId]	string 	Идентификатор пункта самовывоза
orders[][delivery][data][pickuppointSchedule]	string 	Режим работы пункта самовывоза
orders[][delivery][data][pickuppointPhone]	string 	Телефон пункта самовывоза
orders[][delivery][data][payerType]	string 	Плательщик за доставку
orders[][delivery][data][statusComment]	string 	Комментарий к статусу доставки
orders[][delivery][data][cost]	float 	Стоимость доставки, полученная из службы доставки (в валюте объекта)
orders[][delivery][data][minTerm]	integer 	Минимальный срок доставки
orders[][delivery][data][maxTerm]	integer 	Максимальный срок доставки
orders[][delivery][data][shipmentpointId]	string 	Идентификатор терминала отгрузки
orders[][delivery][data][shipmentpointName]	string 	Наименование терминала отгрузки
orders[][delivery][data][shipmentpointAddress]	string 	Адрес терминала отгрузки
orders[][delivery][data][shipmentpointSchedule]	string 	Режим работы терминала отгрузки
orders[][delivery][data][shipmentpointPhone]	string 	Телефон терминала отгрузки
orders[][delivery][data][shipmentpointCoordinateLatitude]	string 	Координаты терминала отгрузки, широта
orders[][delivery][data][shipmentpointCoordinateLongitude]	string 	Координаты терминала отгрузки, долгота
orders[][delivery][data][pickuppointName]	string 	Наименование пункта самовывоза
orders[][delivery][data][pickuppointCoordinateLatitude]	string 	Координаты ПВЗ, широта
orders[][delivery][data][pickuppointCoordinateLongitude]	string 	Координаты ПВЗ, долгота
orders[][delivery][data][extraData]	array 	Дополнительные данные доставки (deliveryDataField.code => значение)
orders[][delivery][data][itemDeclaredValues][]	array of objects (DeclaredValueItem) 	
orders[][delivery][data][itemDeclaredValues][][orderProduct]	object (PackageItemOrderProduct) 	Позиция в заказе
orders[][delivery][data][itemDeclaredValues][][orderProduct][id]	integer 	ID позиции в заказе
orders[][delivery][data][itemDeclaredValues][][orderProduct][externalId]	string 	deprecated Внешний ID позиции в заказе
orders[][delivery][data][itemDeclaredValues][][orderProduct][externalIds][]	array of objects (CodeValueModel) 	Внешние идентификаторы позиции в заказе
orders[][delivery][data][itemDeclaredValues][][orderProduct][externalIds][][code]	string 	Код
orders[][delivery][data][itemDeclaredValues][][orderProduct][externalIds][][value]	string 	Значение
orders[][delivery][data][itemDeclaredValues][][value]	double 	Объявленная стоимость товара
orders[][delivery][data][packages][]	array of objects (Package) 	Упаковки
orders[][delivery][data][packages][][packageId]	string 	Идентификатор упаковки
orders[][delivery][data][packages][][weight]	double 	Вес г.
orders[][delivery][data][packages][][length]	integer 	Длина мм.
orders[][delivery][data][packages][][width]	integer 	Ширина мм.
orders[][delivery][data][packages][][height]	integer 	Высота мм.
orders[][delivery][data][packages][][items][]	array of objects (PackageItem) 	Содержимое упаковки
orders[][delivery][data][packages][][items][][orderProduct]	object (PackageItemOrderProduct) 	Позиция в заказе
orders[][delivery][data][packages][][items][][orderProduct][id]	integer 	ID позиции в заказе
orders[][delivery][data][packages][][items][][orderProduct][externalId]	string 	deprecated Внешний ID позиции в заказе
orders[][delivery][data][packages][][items][][orderProduct][externalIds][]	array of objects (CodeValueModel) 	Внешние идентификаторы позиции в заказе
orders[][delivery][data][packages][][items][][orderProduct][externalIds][][code]	string 	Код
orders[][delivery][data][packages][][items][][orderProduct][externalIds][][value]	string 	Значение
orders[][delivery][data][packages][][items][][quantity]	double 	Количество товара в упаковке
orders[][delivery][service]	object (SerializedDeliveryService) 	
orders[][delivery][service][name]	string 	Название
orders[][delivery][service][code]	string 	Символьный код
orders[][delivery][service][active]	boolean 	Статус активности
orders[][delivery][cost]	double 	Стоимость доставки
orders[][delivery][netCost]	double 	Себестоимость доставки
orders[][delivery][date]	DateTime 	Дата доставки
orders[][delivery][time]	object (TimeInterval) 	Информация о временном диапазоне
orders[][delivery][time][from]	DateTime 	Время "с"
orders[][delivery][time][to]	DateTime 	Время "до"
orders[][delivery][time][custom]	string 	Временной диапазон в свободной форме
orders[][delivery][address]	object (OrderDeliveryAddress) 	Адрес доставки
orders[][delivery][address][index]	string 	Индекс
orders[][delivery][address][countryIso]	string 	ISO код страны
orders[][delivery][address][region]	string 	Регион
orders[][delivery][address][regionId]	integer 	Идентификатор региона в Geohelper
orders[][delivery][address][city]	string 	Город
orders[][delivery][address][cityId]	integer 	Идентификатор города в Geohelper
orders[][delivery][address][cityType]	string 	Тип населенного пункта
orders[][delivery][address][street]	string 	Улица
orders[][delivery][address][streetId]	integer 	Идентификатор улицы в Geohelper
orders[][delivery][address][streetType]	string 	Тип улицы
orders[][delivery][address][building]	string 	Дом
orders[][delivery][address][flat]	string 	Номер квартиры/офиса
orders[][delivery][address][floor]	integer 	Этаж
orders[][delivery][address][block]	integer 	Подъезд
orders[][delivery][address][house]	string 	Строение
orders[][delivery][address][housing]	string 	Корпус
orders[][delivery][address][metro]	string 	Метро
orders[][delivery][address][notes]	string 	Примечания к адресу
orders[][delivery][address][text]	string 	Адрес в текстовом виде
orders[][delivery][vatRate]	string 	Ставка НДС
orders[][site]	string 	Магазин
orders[][status]	string 	Статус заказа
orders[][statusComment]	string 	Комментарий к последнему изменению статуса
orders[][source]	object (SerializedSource) 	Источник заказа
orders[][source][source]	string 	Источник
orders[][source][medium]	string 	Канал
orders[][source][campaign]	string 	Кампания
orders[][source][keyword]	string 	Ключевое слово
orders[][source][content]	string 	Содержание кампании
orders[][items][]	array of objects (OrderProduct) 	Позиция в заказе
orders[][items][][externalId]	string 	deprecated Внешний ID позиции в заказе
orders[][items][][bonusesChargeTotal]	double 	Количество списанных бонусов
orders[][items][][bonusesCreditTotal]	double 	Количество начисленных бонусов
orders[][items][][markingCodes][]	array of strings 	deprecated Коды маркировки. Используйте markingObjects
orders[][items][][markingObjects][]	array of objects (OrderProductMarkingCode) 	Данные маркировки
orders[][items][][markingObjects][][code]	string 	Значение кода маркировки
orders[][items][][markingObjects][][provider]	custom handler result for (enum) 	Тип кода маркировки. Возможные значения: chestny_znak, giis_dmdk
orders[][items][][id]	integer 	ID позиции в заказе
orders[][items][][externalIds][]	array of objects (CodeValueModel) 	Внешние идентификаторы позиции в заказе
orders[][items][][externalIds][][code]	string 	Код
orders[][items][][externalIds][][value]	string 	Значение
orders[][items][][priceType]	object (PriceType) 	Тип цены
orders[][items][][priceType][code]	string 	Код типа цены
orders[][items][][initialPrice]	double 	Цена товара/SKU (в валюте объекта)
orders[][items][][discounts][]	array of objects (AbstractDiscount) 	Массив скидок
orders[][items][][discounts][][type]	string 	Тип скидки. Возможные значения:
manual_order - Разовая скидка на заказ;
manual_product - Дополнительная скидка на товар;
loyalty_level - Скидка по уровню программы лояльности;
loyalty_event - Скидка по событию программы лояльности;
personal - Персональная скидка;
bonus_charge - Списание бонусов ПЛ;
round - Скидка от округления
orders[][items][][discounts][][amount]	float 	Сумма скидки
orders[][items][][discountTotal]	double 	Итоговая денежная скидка на единицу товара c учетом всех скидок на товар и заказ (в валюте объекта)
orders[][items][][prices][]	array of objects (OrderProductPriceItem) 	Набор итоговых цен реализации с указанием количества
orders[][items][][prices][][price]	float 	Итоговая цена c учетом всех скидок на товар и заказ (в валюте объекта)
orders[][items][][prices][][quantity]	float 	Количество товара по заданной цене
orders[][items][][vatRate]	string 	Ставка НДС
orders[][items][][createdAt]	DateTime 	Дата создания позиции в системе
orders[][items][][quantity]	float 	Количество
orders[][items][][status]	string 	Статус позиции в заказе
orders[][items][][comment]	string 	Комментарий к позиции в заказе
orders[][items][][offer]	object (Offer) 	Торговое предложение
orders[][items][][offer][displayName]	string 	Название SKU
orders[][items][][offer][id]	integer 	ID торгового предложения
orders[][items][][offer][externalId]	string 	ID торгового предложения в магазине
orders[][items][][offer][xmlId]	string 	ID торгового предложения в складской системе
orders[][items][][offer][name]	string 	Название
orders[][items][][offer][article]	string 	Артикул
orders[][items][][offer][vatRate]	string 	Ставка НДС
orders[][items][][offer][properties][]	array 	Свойства SKU
orders[][items][][offer][unit]	object (Unit) 	Единица измерения
orders[][items][][offer][unit][code]	string 	Символьный код
orders[][items][][offer][unit][name]	string 	Название
orders[][items][][offer][unit][sym]	string 	Краткое обозначение
orders[][items][][offer][barcode]	string 	Штрих-код
orders[][items][][isCanceled]	boolean 	Данная позиция в заказе является отменной
orders[][items][][properties][]	array 	[массив] Дополнительные свойства позиции в заказе
orders[][items][][properties][][code]	string 	Код свойства (не обязательное поле, код может передаваться в ключе свойства)
orders[][items][][properties][][name]	string 	Имя свойства
orders[][items][][properties][][value]	string 	Значение свойства
orders[][items][][purchasePrice]	double 	Закупочная цена (в базовой валюте)
orders[][items][][ordering]	integer 	Порядок
orders[][fullPaidAt]	DateTime 	Дата полной оплаты
orders[][payments][]	array of objects (Payment) 	Платежи
orders[][payments][][id]	integer 	Внутренний ID
orders[][payments][][status]	string 	Статус оплаты
orders[][payments][][type]	string 	Тип оплаты
orders[][payments][][externalId]	string 	Внешний ID платежа
orders[][payments][][amount]	double 	Сумма платежа (в валюте объекта)
orders[][payments][][paidAt]	DateTime 	Дата оплаты
orders[][payments][][comment]	string 	Комментарий
orders[][fromApi]	boolean 	Заказ поступил через API
orders[][weight]	double 	Вес
orders[][length]	integer 	Длина
orders[][width]	integer 	Ширина
orders[][height]	integer 	Высота
orders[][shipmentStore]	string 	Склад отгрузки
orders[][shipmentDate]	DateTime 	Дата отгрузки
orders[][shipped]	boolean 	Заказ отгружен
orders[][links][]	array of objects (OrderLink) 	Связь заказов
orders[][links][][order]	object (LinkedOrder) 	Связанный заказ
orders[][links][][order][id]	integer 	ID связанного заказа
orders[][links][][order][number]	string 	Номер связанного заказа
orders[][links][][order][externalId]	string 	Внешний ID связанного заказа
orders[][links][][createdAt]	DateTime 	Дата/время создания связи с заказом
orders[][links][][comment]	string 	Комментарий
orders[][customFields]	array 	Ассоциативный массив пользовательских полей
orders[][clientId]	string 	Метка клиента Google Analytic