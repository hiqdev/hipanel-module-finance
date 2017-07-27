# hiqdev/hipanel-module-finance commits history

## [Under development]

    - [e8b32d5] 2017-06-20 csfixed [@hiqsol]
    - [f7d790c] 2017-06-20 renamed `web` config <- hisite [@hiqsol]
    - [6036feb] 2017-06-20 renamed `hidev.yml` [@hiqsol]
    - [c72fcfe] 2017-06-20 Implemented certifiacte tariff management [@SilverFire]
    - [834dc74] 2017-06-16 BillQty factory modifications [@tafid]
    - [9f3f4c0] 2017-06-16 XXX to be checked; quickfixed bill import [@hiqsol]
    - [06508d7] 2017-06-16 fixed finding clients by logins [@hiqsol]
    - [3142ade] 2017-06-16 changed merchant Collection to work for guests and added caching [@hiqsol]
    - [c120775] 2017-06-14 Fixed BillGridView return result if quantity type factory does not have a class [@tafid]
    - [cf0c774] 2017-06-14 Working on Simple Factory for BillQuantity [@tafid]
    - [553df88] 2017-06-14 Changed BillGridView::billQuantity() [@tafid]
    - [62dc0f9] 2017-06-14 redone AvailableMerchants widget to remove doubles and removed use of ArraySpoiler [@hiqsol]
    - [099e33e] 2017-06-13 Fixed. Added missing ArraySpoiler use. [@tafid]
    - [b535da4] 2017-06-13 added tariff and object to bill details page [@hiqsol]
    - [f33ee1f] 2017-06-13 added showing quantity at bill details page [@hiqsol]
    - [8301f0f] 2017-06-13 added exception catch at document generation [@hiqsol]
    - [d3fa24b] 2017-06-12 Added Bill view [@tafid]
    - [0a36716] 2017-06-12 Added ChargeGridView [@tafid]
    - [1d2fea7] 2017-06-12 Added additional attributes to Charge model [@tafid]
    - [1be3f66] 2017-06-12 Added to BillController view action `on beforePerform` to join charges [@tafid]
    - [136e869] 2017-06-09 Fixed Delete action in BillController [@tafid]
    - [2bc2ecb] 2017-06-08 Added check access role `manage` to Sale [@tafid]
    - [8d087f0] 2017-06-07 Doing Bill view [@tafid]
    - [bc0843f] 2017-06-07 Added actions to Bill grid [@tafid]
    - [137830f] 2017-06-07 Added menus for Bill [@tafid]
    - [28b78f5] 2017-06-07 Change design for Bill create `_form` [@tafid]
    - [eba92de] 2017-06-06 Fix time change after update in Bill [@tafid]
    - [8e9a86f] 2017-06-06 Fixed `Update` url in bill index page [@tafid]
    - [e371deb] 2017-05-31 Fixed spelling, tariff notes [@SilverFire]
    - [1993b92] 2017-05-30 fixed typo [@hiqsol]
    - [62d2ea6] 2017-05-22 Removed useless code [@tafid]
    - [880a00e] 2017-05-15 translations [@tafid]
    - [e007e25] 2017-05-15 Added view action to SaleController [@tafid]
    - [68dd9c8] 2017-05-15 Added object column for Sale view [@tafid]
    - [f6c8da6] 2017-05-15 Added Sale view [@tafid]
    - [2435eaf] 2017-05-12 Added `View` button to main column of Sale index [@tafid]
    - [efdf8fe] 2017-05-11 Implemented bill charges [@SilverFire]
    - [2036d74] 2017-05-12 translations [@tafid]
    - [3d3abc6] 2017-05-12 Added translations for Sale [@tafid]
    - [2ed2060] 2017-05-12 Removed `buyer` from sort array [@tafid]
    - [2631265] 2017-05-12 Added new attribute `object_like`, made types as const [@tafid]
    - [b6c3859] 2017-05-12 Removed sorting for tariff seller and object [@tafid]
    - [c8d9709] 2017-05-12 Fixed links for DocumentsColumn and MonthlyDocumentsColumn [@tafid]
    - [b8a38aa] 2017-05-11 Added search fields to Sale [@tafid]
    - [f9920d1] 2017-05-11 Added LinkToObjectResolver widget for Sale [@tafid]
    - [74502bd] 2017-05-11 Added scaffolding for Sale [@tafid]
    - [95317ce] 2017-05-08 Added Sale link to SidebarMenu [@tafid]
    - [fa2818e] 2017-05-08 Removed unnecessary use from controller classes [@tafid]
    - [167b818] 2017-05-05 translation [@tafid]
    - [e9fc156] 2017-05-05 Removed MonthlyDocumentsColumn::getSeeNewRoute() [@tafid]
    - [84ac6db] 2017-05-05 Applied DocumentByMonthButton widget in DocumentsColumn [@tafid]
    - [2e1537d] 2017-05-05 Added PusreController::actionPreGenerateDocument() [@tafid]
    - [04f67e3] 2017-05-05 Added rules and attribute label for `month` attribute [@tafid]
    - [9826ce9] 2017-05-05 Created DocumentByMonthButton widget [@tafid]
    - [0e06282] 2017-05-04 Changed DocumentsColumn design [@tafid]
    - [bf211c8] 2017-04-26 fixed showing quantity for monthly [@hiqsol]
    - [1f91f11] 2017-04-26 lang [@hiqsol]
    - [3c00bba] 2017-04-25 added quantity in bill form [@hiqsol]
    - [abb0784] 2017-04-25 added properer show for monthly quantity in BillGridView [@hiqsol]
    - [9732961] 2017-04-12 Removed set-orientation action [@tafid]
    - [327bbda] 2017-04-10 Created AvailableMerchants widget [@tafid]
    - [c3bb5ee] 2017-04-05 Enhanced documents routes [@SilverFire]
    - [0a64ef4] 2017-03-31 minor fix [@SilverFire]
    - [ec26015] 2017-03-31 Used Connection::callWithDisabledAuth() [@SilverFire]
    - [74a8abd] 2017-03-31 Added ApiTransactionRepository::callWithoutAuth() [@SilverFire]
    - [1ba8e64] 2017-03-31 Updated to follow API changes [@SilverFire]
    - [12261e0] 2017-03-30 Added yandexmoney support [@SilverFire]
    - [8cc55ef] 2017-03-30 Implemented ApiTransactionRepository [@SilverFire]
    - [15775ce] 2017-03-29 added require `hiqdev/omnipay-freekassa` [@hiqsol]
    - [2225324] 2017-03-29 added freekassa [@hiqsol]
    - [b34cc4d] 2017-03-29 changed Collection::convertMerchant: added passing all the available data (was only 5 fields) [@hiqsol]
    - [4c33eb7] 2017-03-28 Updated merchant\Collection::convertMerchant() to follow php-merchant API changes [@SilverFire]
    - [5308a40] 2017-03-28 Added Yandex money to merchants collection [@SilverFire]
    - [ee85930] 2017-03-27 enabled okpay merchant [@hiqsol]
    - [3085624] 2017-03-16 Refactored DocumentsColumn to add management buttons to the label of column [@SilverFire]
    - [f0fcb7b] 2017-03-06 fixed TariffCombo to user ilike for primary filter [@hiqsol]
    - [34b53d4] 2017-03-03 Implemented currency exchange [@SilverFire]
    - [76d5968] 2017-03-02 added InterKassa to supportedSystems and added ordering [@hiqsol]
    - [7715b59] 2017-03-01 translation [@hiqsol]
    - [851765c] 2017-02-28 showing properly purse invoices and acceptance reports with DocumentColumn [@hiqsol]
    - [873c221] 2017-02-28 added `DocumentsColumn` [@hiqsol]
    - [0272daf] 2017-02-28 fix unit name (#2) [@BladeRoot]
    - [ed433d4] 2017-02-24 Updated DomainTariffForm to throw IntegrityException on resources count mismatch [@SilverFire]
    - [bb26cdd] 2017-02-24 Enhanced AbstractTariffManager, AbstractTariffForm not to fail when parent tariff is not available [@SilverFire]
    - [a45d972] 2017-02-24 Added IntegrityException [@SilverFire]
    - [ab7c287] 2017-02-22 Updated AbstractTariffForm to thorow exception when no parent tariff is available [@SilverFire]
    - [546d988] 2017-02-21 improved purse documents <- invoices only [@hiqsol]
    - [aee4750] 2017-02-19 PHPdocs enhanced [@SilverFire]
    - [ad8d628] 2017-02-20 removed use of `hipanel\grid\DataColumn` in favour of `hiqdev\higrid\DataColumn` [@hiqsol]
    - [497cca2] 2017-02-20 fixed sum_editable column in bill grid, added can update bill check [@hiqsol]
    - [d60d1df] 2017-02-17 removed empty non-working BillControllerTest [@hiqsol]
    - [706d380] 2017-02-17 csfixed [@hiqsol]
    - [bffcbd1] 2017-02-17 redone generate/update invoice -> generate/and-save document [@hiqsol]
    - [117392d] 2017-02-16 Updated translations [@SilverFire]
    - [13ae642] 2017-02-16 Added themeMenager settings [@tafid]
    - [f4eec0b] 2017-02-15 Added omipay packages [@tafid]
    - [9b7eb74] 2017-02-15 Updated search action in controller in order to use ComboSearchAction [@SilverFire]
    - [7957530] 2017-02-14 Updated AbstractTariffManager::findParentTariffs() to follow HiArt API changes [@SilverFire]
    - [3770db4] 2017-02-12 Added Tariff::getStubResource() [@SilverFire]
    - [5701cde] 2017-02-14 Fixed DomainTariffManager to follow HiArt API changes [@SilverFire]
    - [016bf91] 2017-02-14 Enhanced PriceDifferenceWidget not to disaplay difference, when it is empty [@SilverFire]
    - [30851eb] 2017-02-14 translation [@hiqsol]
    - [06e8c32] 2017-02-14 added requisites link in menu [@hiqsol]
    - [ad5800c] 2017-02-13 added acceptance report generation [@hiqsol]
    - [ad5f152] 2017-02-13 Added `type` column to the TariffsGridView [@SilverFire]
    - [0e217f1] 2017-02-10 Updated widgets and views to follow yii2-combo API changes [@SilverFire]
    - [0b2f89c] 2017-02-01 Refactored PayButtonComment: removed bootsrap, implemented with behaviors [@SilverFire]
    - [20e8664] 2017-02-01 Implemented PayButton::EVENT_RENDER_COMMENT handler [@SilverFire]
    - [0b8c6eb] 2017-01-31 renamed scenarioActions <- scenarioCommands [@hiqsol]
    - [67e3249] 2017-01-30 Renamed HiArtException to hiqdev\hiart\Exception [@SilverFire]
    - [d71e56e] 2017-01-30 Fixed ResponseErrorException catching block [@SilverFire]
    - [e33def5] 2017-01-30 Registered BillTypesProvider as a singleton in the DIC [@SilverFire]
    - [c87d76b] 2017-01-30 Fixed accidentally commited condition [@SilverFire]
    - [7902940] 2017-01-30 renamed hiqdev\\hiart\\ResponseErrorException <- ErrorResponseException [@hiqsol]
    - [ebab548] 2017-01-30 removed use of ApiConnectionInterface [@hiqsol]
    - [4563290] 2017-01-27 Translations updated [@SilverFire]
    - [c692859] 2017-01-27 renamed from -> `tableName` in ActiveRecord [@hiqsol]
    - [37f66ff] 2017-01-27 changed index/type -> `from` in ActiveRecord [@hiqsol]
    - [bf59688] 2017-01-27 Translations updated [@SilverFire]
    - [41674b1] 2016-01-24 Fixed `time` column in BillGridView to show only month name for special bill types [@SilverFire]
    - [14088ce] 2017-01-24 fixed hiart `perform()` usage [@hiqsol]
    - [ef93622] 2017-01-20 csfixed [@SilverFire]
    - [f035973] 2017-01-20 Changed merchant/Collection to pass the `commission_fee` [@SilverFire]
    - [d3abd8c] 2017-01-19 Fixed bills fitlering by full type [@SilverFire]
    - [75f5f12] 2017-01-16 PHPDocs updated [@SilverFire]
    - [c24b485] 2017-01-12 Merge pull request #1 from bladeroot/finance-datetime [@SilverFire]
    - [71f168d] 2017-01-12 fix datetime format: en localization format time 12:00 to 00:00 [@BladeRoot]
    - [581abd9] 2016-12-29 rename rest -> budget and fixed problem with fraction in budget [@hiqsol]
    - [aeff303] 2016-12-26 Updated translations [@SilverFire]
    - [8c1c912] 2016-12-22 redone yii2-thememanager -> yii2-menus [@hiqsol]
    - [8df6d6d] 2016-12-21 Attached BillNegation behavior [@tafid]
    - [706dd85] 2016-12-21 Added custom validation for `sum` attribute [@tafid]
    - [39961d3] 2016-12-21 Added BillNegation behavior [@tafid]
    - [7a3efdc] 2016-12-21 redone Menus: widget instead of create+render [@hiqsol]
    - [4f3c1bc] 2016-12-21 moved menus definitions to DI [@hiqsol]
    - [0d0c353] 2016-12-16 Implemented contact changing for purses [@SilverFire]
    - [7131b7a] 2016-12-15 Follow changes in ContactCombo API [@SilverFire]
    - [eb64f25] 2016-12-13 Implemented bill types filtering depending on client group [@SilverFire]
    - [e07b14b] 2016-12-09 + ComboXEditable for purse requisite [@hiqsol]
    - [1df109c] 2016-11-30 + properly show requisite [@hiqsol]
    - [72c3049] 2016-11-29 Added new Menu [@tafid]
    - [72ce260] 2016-11-28 used `joinWith` for files, contact and requisite [@hiqsol]
    - [7b333d0] 2016-11-17 Translations updated [@SilverFire]
    - [eb9bbd9] 2016-11-17 Added setOrientation action to HeldPaymentsController [@SilverFire]
    - [e6d0475] 2016-11-17 translation [@hiqsol]
    - [be07663] 2016-11-17 fix [@tafid]
    - [a6b2c6c] 2016-11-17 translations [@tafid]
    - [ed32789] 2016-11-17 Fixed time field if isNewRecord [@tafid]
    - [d7a278b] 2016-11-16 translations [@tafid]
    - [073839c] 2016-11-16 fixed permissions names to new style: bill.create/update/delete [@hiqsol]
    - [6b97773] 2016-11-16 Enhance Purchase buttons lock after pressing to prevent double-submit [@SilverFire]
    - [4b89d71] 2016-11-15 Tariff view: hid tariff details information from non-managers [@SilverFire]
    - [e0392db] 2016-11-15 Changed translation dictionaries [@tafid]
    - [2d14d19] 2016-11-15 Changed transaltion dictionaries [@tafid]
    - [cc37e0d] 2016-11-11 Added Purchase buttons lock after pressing to prevent double-submit [@SilverFire]
    - [27fb148] 2016-11-11 Update Calculation not to override set client and seller [@SilverFire]
    - [6d11c76] 2016-11-09 Enhanced filtering for bills currency and type [@SilverFire]
    - [519d4a0] 2016-11-07 Implemented bills copying [@SilverFire]
    - [9747b20] 2016-11-07 Finalized bills import [@SilverFire]
    - [0211a32] 2016-11-04 Implemented bills editing and importing [@SilverFire]
    - [96c4d74] 2016-11-04 finished multiple type in bill filter [@hiqsol]
    - [284371a] 2016-11-04 + multiple type in bill filter [@hiqsol]
    - [1706a0a] 2016-11-02 Changed AmountWithCurrency widget [@tafid]
    - [e3fded1] 2016-11-02 Hide `Recharge accounnt` button when user does not have the `deposite` permission [@SilverFire]
    - [22f970a] 2016-11-01 fixed date format for DatePicker [@hiqsol]
    - [94b1399] 2016-10-31 fixed bill related permissions [@hiqsol]
    - [91cf720] 2016-10-28 Added translation for search attribute for Bill [@tafid]
    - [d4aa727] 2016-10-28 Added translation and Used label [@tafid]
    - [cd215e0] 2016-10-25 translations [@hiqsol]
    - [73585bf] 2016-10-21 Translations updated [@SilverFire]
    - [109ed86] 2016-10-05 Added AccessControl filter to CartContoller [@SilverFire]
    - [f5d3631] 2016-10-04 Changed params->seller to params->user.seller [@SilverFire]
    - [dc8c4b6] 2016-09-30 Updated related classes to follow Calculator class API changes [@SilverFire]
    - [75f2505] 2016-09-30 CartCalculator now extends Calculator [@SilverFire]
    - [0a09008] 2016-09-30 AbstractCartPosition implements CalculableInterface [@SilverFire]
    - [e0553ed] 2016-09-30 Added model\Calculation::calculation_id [@SilverFire]
    - [c96eebb] 2016-09-29 TariffCalculator -> Calculator [@SilverFire]
    - [d9c24b5] 2016-09-27 fixed param organizationUrl <- orgUrl [@hiqsol]
    - [e926398] 2016-09-26 Added translations, added LocationResourceDecorator, changed BooleanInput class to OptionsInput class [@tafid]
    - [f6b39d9] 2016-09-22 minor renaming [@hiqsol]
    - [9c007bc] 2016-09-22 removed unused hidev config [@hiqsol]
    - [1cb7117] 2016-09-22 redone menu to new style [@hiqsol]
    - [ec41910] 2016-09-22 removed old junk Plugin.php [@hiqsol]
    - [0b05637] 2016-09-22 Implemented personal tariff editing [@SilverFire]
    - [ecb3c3e] 2016-09-20 Renamed `baseTariff`(/s) to `parentTariff`(s), other minor [@SilverFire]
    - [b074591] 2016-09-19 reviewed [@hiqsol]
    - [7f3712e] 2016-09-16 Updated PHPDoc [@SilverFire]
    - [dc93ab3] 2016-09-16 Implemented tariff price difference information [@SilverFire]
    - [77b1bbe] 2016-09-13 Modified tariffs management code to follow module API changes [@SilverFire]
    - [bf466a7] 2016-09-13 Added TariffCalculator and Value models [@SilverFire]
    - [5b6f419] 2016-09-13 Added cart/Calculator basic model [@SilverFire]
    - [5a5e4be] 2016-09-09 Removed FieldRange widget usage [@SilverFire]
    - [8351d4b] 2016-09-08 Added OVDS views [@SilverFire]
    - [91e0faa] 2016-09-08 Implemented OVDS tariffs creation. Updated PHPDocs, minor API optimizations [@SilverFire]
    - [3ae07e0] 2016-09-08 Added Panel, Sepped, Support resources decorators [@SilverFire]
    - [707314b] 2016-09-08 Added ServerResource sutb [@SilverFire]
    - [7d30b9e] 2016-09-08 Updated translations [@SilverFire]
    - [b87a46b] 2016-09-07 Implemented VDS tariffs management [@SilverFire]
    - [018a2f4] 2016-09-06 Implementing VDS tariff creating [@SilverFire]
    - [0622f1d] 2016-09-05 Continue work on server tariffs management [@SilverFire]
    - [ef2c0cc] 2016-09-01 Created stubs for resource decorators [@SilverFire]
    - [83485fb] 2016-08-31 Partially implemented VDS tariffs management [@SilverFire]
    - [0edfe96] 2016-08-31 Removed COMPOSER_CONFIG_PLUGIN_DIR [@tafid]
    - [7164295] 2016-08-26 Improved AbstractTariffForm, AbstractTariffManager and their child classes. Started VDS tariffs [@SilverFire]
    - [b2cff45] 2016-08-25 Finished domain tariffs editing [@SilverFire]
    - [e3a0660] 2016-08-24 redone subtitle to original Yii style [@hiqsol]
    - [694418f] 2016-08-23 redone breadcrumbs to original Yii style [@hiqsol]
    - [89eb044] 2016-08-19 Implemented domain services view [@SilverFire]
    - [dad9027] 2016-08-19 Updated messages [@SilverFire]
    - [030ae6c] 2016-08-19 Improved tariffs management architecture [@SilverFire]
    - [844c418] 2016-08-18 Implemented domain tariffs view [@SilverFire]
    - [83d0f02] 2016-08-18 Translations updated [@SilverFire]
    - [0998523] 2016-08-18 Implemeted domain tariff management [@SilverFire]
    - [0012a2c] 2016-08-02 fixed translations app -> hipanel [@hiqsol]
    - [35273b6] 2016-07-25 Added tariff dictionary [@SilverFire]
    - [ef5cdd9] 2016-07-21 Removed Client and Seller filters from the AdvancedSearch view for non-support [@SilverFire]
    - [ffdb66e] 2016-07-21 Translations updated [@SilverFire]
    - [14391bd] 2016-07-13 renamed AmountWithCurrency <- AmountWithCurrencyWidget [@hiqsol]
    - [3e99904] 2016-07-13 currency made required on create [@hiqsol]
    - [5358194] 2016-07-13 Fixed design in bill create form [@tafid]
    - [ac2069c] 2016-07-13 added dynamic form [@hiqsol]
    - [44a8327] 2016-07-12 made time required on create [@hiqsol]
    - [3cbdc11] 2016-07-08 csfixed [@hiqsol]
    - [d3dc0ea] 2016-07-07 improved bill filtering, added server filter [@hiqsol]
    - [09d5c87] 2016-07-07 improved showing consumed resources at bill index [@hiqsol]
    - [7d2c3ca] 2016-07-06 improved bill description column with nobrs [@hiqsol]
    - [c168d11] 2016-07-05 fixed time_from/till filtering [@hiqsol]
    - [5ea29a4] 2016-06-30 Removed dependency on Err class [@SilverFire]
    - [8207821] 2016-06-21 Fix design in cart/select [@tafid]
    - [28f1fc7] 2016-06-16 Changed Ref::getList to $this->getRefs in DomainConteoller [@SilverFire]
    - [ab5227b] 2016-06-16 Updated translations [@SilverFire]
    - [374da43] 2016-06-16 csfixed [@hiqsol]
    - [5f375d3] 2016-06-16 allowed build failure for PHP 5.5 [@hiqsol]
    - [23d4574] 2016-06-15 removed use of `2amigos/yii2-date-time-picker-widget` [@hiqsol]
    - [52f95e3] 2016-06-15 tidying up kartik widgets [@hiqsol]
    - [e68327d] 2016-06-12 Added backup amount display to bills grid view [@SilverFire]
    - [c2b6e42] 2016-06-11 Updated translations [@SilverFire]
    - [d19dbfb] 2016-06-10 Updated translations [@SilverFire]
    - [cc9eb10] 2016-06-09 Switch to IndexPage layout [@tafid]
    - [ee2c76a] 2016-06-08 Held payments implemented [@SilverFire]
    - [aceb9ca] 2016-06-08 Updated SidebarMenu [@SilverFire]
    - [b6c142a] 2016-06-08 Updated translations [@SilverFire]
    - [5de8396] 2016-06-07 Added STATE constants to Change module [@SilverFire]
    - [d57e692] 2016-06-07 Updated translations [@SilverFire]
    - [1e3482a] 2016-06-05 lang [@hiqsol]
    - [8ae4c5d] 2016-06-03 Updated translations [@SilverFire]
    - [071be19] 2016-06-01 Added changes support [@SilverFire]
    - [033e7e4] 2016-06-02 + require 2amigos/yii2-date-time-picker-widget [@hiqsol]
    - [f793c97] 2016-06-01 + TariffCombo [@hiqsol]
    - [5280b4b] 2016-06-01 + search action [@hiqsol]
    - [b139aad] 2016-06-01 used hiqdev/hidev-hiqdev [@hiqsol]
    - [670e60c] 2016-06-01 Change index page layout [@tafid]
    - [907d4ee] 2016-05-31 Add OrientationAction to controllers [@tafid]
    - [13f02fb] 2016-05-26 Rdesign cart/select page [@tafid]
    - [a43a895] 2016-05-26 Updated views cart/select, cart/finish to use best practices of messages translation [@SilverFire]
    - [64b5f68] 2016-05-26 Added @finance alias [@SilverFire]
    - [9399360] 2016-05-26 Updated translations [@SilverFire]
    - [d785b73] 2016-05-26 Fixed CartFinisher::run() to save Purchase object into success array [@SilverFire]
    - [92caf2f] 2016-05-25 Added PositionFinishExceptionInterface [@SilverFire]
    - [a3bb73c] 2016-05-24 Updated CartFinisher - added pending operations support [@SilverFire]
    - [cd20091] 2016-05-24 Cart finish view: updated to use Position::renderDescription(), added Pending operations block [@SilverFire]
    - [0960768] 2016-05-24 Changed ErrorPurchaseException::__construct to use Purchase object instead of position [@SilverFire]
    - [436f24b] 2016-05-24 Added PendingPurchaseException [@SilverFire]
    - [0017b4d] 2016-05-23 CartFinisher fixed to catch validation errors [@SilverFire]
    - [6eefbd4] 2016-05-23 ErrorPurchaseException - changed to store Position insead of Purchage object [@SilverFire]
    - [3ece88a] 2016-05-19 Get seller from params in Calculation [@tafid]
    - [1fac83b] 2016-05-18 fixing dependencies constraints [@hiqsol]
    - [0322f0d] 2016-05-18 fixed menus [@hiqsol]
    - [c289c75] 2016-05-18 used composer-config-plugin [@hiqsol]
    - [899f594] 2016-05-11 Updated composer.json - changed url to asset-packagist.org [@SilverFire]
    - [96de292] 2016-05-11 Updated namespace of $module in PayController - fixed code highlightin [@SilverFire]
    - [1fbcee9] 2016-05-11 Fixed PayController to work with the latest hiart release [@SilverFire]
- Added initial tests
    - [7abb971] 2016-04-27 phpcsfixed [@hiqsol]
    - [98ee7ba] 2016-04-27 rehideved [@hiqsol]
    - [8275736] 2016-04-27 added tests [@hiqsol]
- Fixed minor issues
    - [a652515] 2016-04-27 open new invoices in separate tab [@hiqsol]
    - [3095e2c] 2016-04-21 CreditColumn - Fixed URL to set-credit action [@SilverFire]
    - [42358b1] 2016-04-18 fixed completing history [@hiqsol]
    - [5393501] 2016-04-11 Added `label` attribute to the Tariff model [@SilverFire]
    - [57a347b] 2016-02-18 Changed XEditableColumn import namespace [@SilverFire]
    - [ca50518] 2016-02-18 + purse block view [@hiqsol]
    - [ba1f273] 2016-02-18 Added module-scope translations [@hiqsol]
    - [8c9e191] 2016-02-17 + hide finance menu from admins and supports [@hiqsol]
    - [f8a83ed] 2016-02-09 Fix margin in search form [@tafid]
    - [379431c] 2016-02-09 Fix date range field view [@tafid]
    - [2f96dc8] 2015-10-15 PurseGridView - ArraySpoiler call options changed [@SilverFire]
    - [205653c] 2015-10-15 PurseGridView changed call of ArraySpoiler [@SilverFire]
    - [c5781ae] 2015-10-15 PurseGridView changed call of ArraySpoiler [@SilverFire]
    - [8c5f851] 2015-09-15 localized menu [@hiqsol]
    - [bd8967c] 2015-08-28 Added dependencies on related projects [@SilverFire]
    - [204868a] 2015-08-27 Fixed breadcrumbs subtitle [@SilverFire]
    - [ecac786] 2015-08-27 Fixed deprecated method calling syntax [@SilverFire]
    - [84dfe87] 2015-08-26 Redisign view [@tafid]
- Added `CalculableModelInterface`
    - [34d1496] 2016-04-06 Added CalculableModelInterface [@SilverFire]
- Fixed build with asset-packagist
    - [b596356] 2016-04-06 fixed build with asset-packagist [@hiqsol]
    - [6c8b9c9] 2016-04-06 inited tests [@hiqsol]
- Added bills creating and updating
    - [b8b18bc] 2016-04-25 Bills - added extended types filter [@SilverFire]
    - [3141710] 2016-04-01 Added bills creating [@SilverFire]
    - [04f8df2] 2016-03-17 Cart payment methods do not throw warning when no methods available [@SilverFire]
    - [e2913ad] 2016-03-16 Added missing translation [@SilverFire]
    - [10bbc4b] 2016-03-10 Removed bill update page link [@SilverFire]
- Added yii2-merchant integration
    - [162755a] 2016-04-12 marchant/Collection::fetchMerchants() - works for loggged out users. Todo: add merchants for logged out clients [@SilverFire]
    - [31a7709] 2016-04-05 + supported systems list [@hiqsol]
    - [e2bc574] 2016-03-25 fixed eCoin to work, it sends on notify only return [@hiqsol]
    - [e9615f8] 2016-03-10 Fixed recharge account link on bill/index [@SilverFire]
    - [12b5726] 2016-02-04 phpcsfixed [@hiqsol]
    - [2c10dab] 2016-02-04 rehideved [@hiqsol]
    - [5ff8bc4] 2016-02-04 fixed hiding wmdirect [@hiqsol]
    - [57b333f] 2016-02-04 commented out additional payment instructions [@hiqsol]
    - [3060c8b] 2016-02-03 Collection::fetchMerchants() - added a check for a case, when no available payment methods found [@SilverFire]
    - [1d07e87] 2015-12-14 Deposit - added validation rules for sum attribute [@SilverFire]
    - [b8f4e02] 2015-12-10 used Merchant module renderNotify() [@hiqsol]
    - [8330653] 2015-12-10 finishing pay/notify action [@hiqsol]
    - [c818fbb] 2015-12-07 fixed translations [@hiqsol]
    - [210a741] 2015-11-12 php-cs-fixed [@hiqsol]
    - [0391a44] 2015-11-12 improved package description [@hiqsol]
    - [2f6b600] 2015-11-12 still adding integration with yii2-merchant [@hiqsol]
    - [83095c9] 2015-11-09 + PayController [@hiqsol]
    - [6e5c4fd] 2015-10-30 Add Resource model [@tafid]
    - [eb8d63e] 2015-10-30 + Merchant module [@hiqsol]
    - [685eb2c] 2015-10-30 + Merchant module [@hiqsol]
    - [c14a036] 2015-10-26 - use of local MerchantModule [@hiqsol]
    - [29a6c16] 2015-10-26 - bill deposit action [@hiqsol]
- Added resource usage
    - [588bf23] 2015-10-29 Add relation [@tafid]
    - [0f40b67] 2016-02-23 Added RUse model [@SilverFire]
- Added cart price calculation and finishing (order performing)
    - [0111b05] 2016-04-05 Calculation model - fixed `seller` propery filling when user is guest [@SilverFire]
    - [3e6a3f4] 2016-03-04 phpcsfixed [@hiqsol]
    - [93a3b00] 2016-03-04 fixed removing purchases item from cart [@hiqsol]
    - [77975da] 2016-03-04 + purchase notes and remarks [@hiqsol]
    - [2f999ab] 2016-03-04 improving purchasing NOT FINISHED [@hiqsol]
    - [acb92e9] 2016-02-02 CartCalculator::updatePositions fixed getting position ID [@SilverFire]
    - [0046d4a] 2016-01-29 added tranlations [@hiqsol]
    - [8ab4f5a] 2016-01-18 Redesign cart/finish view [@tafid]
    - [611c2f0] 2016-01-18 Cart finish page - added title [@SilverFire]
    - [da1e45f] 2016-01-15 Cart finish page sketch [@SilverFire]
    - [325f428] 2016-01-15 Removed AbstractPurchase::synchronize(), PHPDoc updated [@SilverFire]
    - [2d6d07f] 2016-01-15 CartFinisher::finish() ~> run(), logic improved [@SilverFire]
    - [bd4adc6] 2016-01-14 Added Calculation::synchronize() method [@SilverFire]
    - [16232ea] 2016-01-14 Added purchaseModel property [@SilverFire]
    - [eea9174] 2016-01-14 Changed cartChange event handler name (CartCalculation -> CartCalculator) [@SilverFire]
    - [cb53d22] 2016-01-14 Added AbstractPurchase, ErrorPurchaseException [@SilverFire]
    - [a62c53e] 2016-01-14 CartCalculator renamed to CartCalculation [@SilverFire]
    - [540d96d] 2016-01-14 Started CartFinisher [@SilverFire]
    - [ae001f6] 2016-01-14 CartCalculator renamed to CartCalculation [@SilverFire]
    - [f34524c] 2015-12-30 Added PHPDoc [@SilverFire]
    - [5968e9b] 2015-12-29 CartCalculation - get rid of hiart/collection using [@SilverFire]
    - [c95729a] 2015-12-25 Added cart price calculation [@SilverFire]
- Added monthly invoices
    - [981b5c2] 2016-02-05 + nocache invoice file [@hiqsol]
    - [3628150] 2015-10-15 + purse update monthly invoice action [@hiqsol]
    - [d56ff71] 2015-10-13 fixed purse invoices displaying [@hiqsol]
    - [ef409ea] 2015-10-13 x hide actions for clients and admins [@BladeRoot]
    - [d193959] 2015-10-09 Model Tariff - added $resurces attribute [@SilverFire]
    - [bd18338] 2015-10-08 Bills: added `time_from/till`, `sum_gt/lt` filter for addvanced serach, other minor [@SilverFire]
    - [5f0a2a4] 2015-10-08 improved pdf invoices archive link [@hiqsol]
    - [604808a] 2015-10-07 + Purse model, controller, grid, alias [@hiqsol]
- Added yii2-cart integration
    - [fae771a] 2015-12-25 fixed PayController::notifyAction to restore main transaction data from history when not passed by payment system [@hiqsol]
    - [d4c6d86] 2015-12-25 used finishUrl instead of returnUrl [@hiqsol]
    - [7c6259c] 2015-12-23 + currency filter and type made multiple [@hiqsol]
    - [5ed3415] 2015-12-16 Added PHPDoc, refactoring [@SilverFire]
    - [50883e0] 2015-12-15 CartController - added returnUrl, Module - added finishPage for Merchant [@SilverFire]
    - [4649c23] 2015-12-04 added cart/finish not finished [@hiqsol]
    - [2220972] 2015-12-04 Classes notation changed from pathtoClassName to PHP 5.6 ClassName::class [@SilverFire]
    - [960f744] 2015-12-03 improved Payment Merchants list: + provided methods visa and maestro [@hiqsol]
    - [c0bc083] 2015-12-01 fixed Payment Methods icons [@hiqsol]
    - [ac5a8e5] 2015-12-01 php-cs-fixed [@hiqsol]
    - [96d7fc3] 2015-12-01 + Cart controller [@hiqsol]
    - [9f7b81c] 2015-11-30 + local payment methods block [@hiqsol]
    - [6c6aebd] 2015-11-30 + local payment methods block [@hiqsol]
    - [486e058] 2015-11-16 php-cs-fixed [@hiqsol]
    - [8cc5d70] 2015-11-16 + AbstractCartPosition [@hiqsol]
    - [c58bee9] 2015-11-13 + terms page link for cart module [@hiqsol]
    - [15aaab0] 2015-11-12 + require yii2-cart [@hiqsol]
    - [bf855b5] 2015-11-12 improved package description [@hiqsol]
    - [7dfe5af] 2015-11-12 added yii2-cart integration [@hiqsol]
- Fixed Bill index page: greatly improved looking
    - [1929274] 2015-10-23 improved Bill index: + object link [@hiqsol]
    - [9c032a6] 2015-10-23 improved Bill index: + object [@hiqsol]
    - [8b8bbfe] 2015-10-07 + CreditColumn [@hiqsol]
    - [57baa40] 2015-10-07 + BalanceColumn [@hiqsol]
    - [08216ec] 2015-09-23 improved type column at Bill, RefColumn used [@hiqsol]
    - [d0b12d1] 2015-09-23 greatly improved Bill index page [@hiqsol]
    - [5145a10] 2015-08-26 fixed bill description column [@hiqsol]
    - [e1b936f] 2015-08-25 Fix icons [@tafid]
- Added bills deleting
    - [7b423b5] 2015-10-22 + delete bills with check for delete-bills permission [@hiqsol]
- Fixed access control
    - [0347d17] 2015-08-26 fixed access control [@hiqsol]
- Added bill/deposit redirect
    - [d3dc841] 2015-08-19 + bill/deposit redirect [@hiqsol]
- Added @bill, @tariff aliases
    - [59020bb] 2015-08-19 + @bill, @tariff aliases [@hiqsol]
- Fixed with all new features: SmartPerformAction, ActionBox
    - [6799146] 2015-08-12 Add per page to Bill and Tariff [@tafid]
    - [f2142e6] 2015-08-06 renamed SmartDeleteAction to SmartPerformAction [@hiqsol]
    - [75f5355] 2015-08-06 Add BoxAction, add serach view [@tafid]
    - [8102422] 2015-08-05 Add ActionBox [@tafid]
    - [6695253] 2015-07-31 + smart actions [@hiqsol]
- Changed: moved to src, hideved and php-cs-fixed
    - [d41091b] 2015-07-22 php-cs-fixed [@hiqsol]
    - [8877ca8] 2015-07-22 quick fixed description at Bill page [@hiqsol]
    - [78cd0cf] 2015-07-22 + .hidev/commits.md [@hiqsol]
    - [b638fc5] 2015-07-21 moved to src [@hiqsol]
- Added basics
    - [f4a9d42] 2015-05-15 + Plugin, * Menu [@hiqsol]
    - [d48a7f0] 2015-05-14 + Menu.php and changed breadcrumbing [@hiqsol]
    - [746e4b5] 2015-04-28 bill module changes [@BladeRoot]
    - [934bf1d] 2015-04-23 * changes in finance [@BladeRoot]
    - [c61d2bb] 2015-04-21 removed excessive files [@hiqsol]
    - [41497e0] 2015-04-21 inited [@hiqsol]
## Development started 2015-04-21

## [Development started] - 2015-04-21

[@hiqsol]: https://github.com/hiqsol
[sol@hiqdev.com]: https://github.com/hiqsol
[@SilverFire]: https://github.com/SilverFire
[d.naumenko.a@gmail.com]: https://github.com/SilverFire
[@tafid]: https://github.com/tafid
[andreyklochok@gmail.com]: https://github.com/tafid
[@BladeRoot]: https://github.com/BladeRoot
[bladeroot@gmail.com]: https://github.com/BladeRoot
[7abb971]: https://github.com/hiqdev/hipanel-module-finance/commit/7abb971
[98ee7ba]: https://github.com/hiqdev/hipanel-module-finance/commit/98ee7ba
[8275736]: https://github.com/hiqdev/hipanel-module-finance/commit/8275736
[a652515]: https://github.com/hiqdev/hipanel-module-finance/commit/a652515
[3095e2c]: https://github.com/hiqdev/hipanel-module-finance/commit/3095e2c
[42358b1]: https://github.com/hiqdev/hipanel-module-finance/commit/42358b1
[5393501]: https://github.com/hiqdev/hipanel-module-finance/commit/5393501
[57a347b]: https://github.com/hiqdev/hipanel-module-finance/commit/57a347b
[ca50518]: https://github.com/hiqdev/hipanel-module-finance/commit/ca50518
[ba1f273]: https://github.com/hiqdev/hipanel-module-finance/commit/ba1f273
[8c9e191]: https://github.com/hiqdev/hipanel-module-finance/commit/8c9e191
[f8a83ed]: https://github.com/hiqdev/hipanel-module-finance/commit/f8a83ed
[379431c]: https://github.com/hiqdev/hipanel-module-finance/commit/379431c
[2f96dc8]: https://github.com/hiqdev/hipanel-module-finance/commit/2f96dc8
[205653c]: https://github.com/hiqdev/hipanel-module-finance/commit/205653c
[c5781ae]: https://github.com/hiqdev/hipanel-module-finance/commit/c5781ae
[8c5f851]: https://github.com/hiqdev/hipanel-module-finance/commit/8c5f851
[bd8967c]: https://github.com/hiqdev/hipanel-module-finance/commit/bd8967c
[204868a]: https://github.com/hiqdev/hipanel-module-finance/commit/204868a
[ecac786]: https://github.com/hiqdev/hipanel-module-finance/commit/ecac786
[84dfe87]: https://github.com/hiqdev/hipanel-module-finance/commit/84dfe87
[34d1496]: https://github.com/hiqdev/hipanel-module-finance/commit/34d1496
[b596356]: https://github.com/hiqdev/hipanel-module-finance/commit/b596356
[6c8b9c9]: https://github.com/hiqdev/hipanel-module-finance/commit/6c8b9c9
[b8b18bc]: https://github.com/hiqdev/hipanel-module-finance/commit/b8b18bc
[3141710]: https://github.com/hiqdev/hipanel-module-finance/commit/3141710
[04f8df2]: https://github.com/hiqdev/hipanel-module-finance/commit/04f8df2
[e2913ad]: https://github.com/hiqdev/hipanel-module-finance/commit/e2913ad
[10bbc4b]: https://github.com/hiqdev/hipanel-module-finance/commit/10bbc4b
[162755a]: https://github.com/hiqdev/hipanel-module-finance/commit/162755a
[31a7709]: https://github.com/hiqdev/hipanel-module-finance/commit/31a7709
[e2bc574]: https://github.com/hiqdev/hipanel-module-finance/commit/e2bc574
[e9615f8]: https://github.com/hiqdev/hipanel-module-finance/commit/e9615f8
[12b5726]: https://github.com/hiqdev/hipanel-module-finance/commit/12b5726
[2c10dab]: https://github.com/hiqdev/hipanel-module-finance/commit/2c10dab
[5ff8bc4]: https://github.com/hiqdev/hipanel-module-finance/commit/5ff8bc4
[57b333f]: https://github.com/hiqdev/hipanel-module-finance/commit/57b333f
[3060c8b]: https://github.com/hiqdev/hipanel-module-finance/commit/3060c8b
[1d07e87]: https://github.com/hiqdev/hipanel-module-finance/commit/1d07e87
[b8f4e02]: https://github.com/hiqdev/hipanel-module-finance/commit/b8f4e02
[8330653]: https://github.com/hiqdev/hipanel-module-finance/commit/8330653
[c818fbb]: https://github.com/hiqdev/hipanel-module-finance/commit/c818fbb
[210a741]: https://github.com/hiqdev/hipanel-module-finance/commit/210a741
[0391a44]: https://github.com/hiqdev/hipanel-module-finance/commit/0391a44
[2f6b600]: https://github.com/hiqdev/hipanel-module-finance/commit/2f6b600
[83095c9]: https://github.com/hiqdev/hipanel-module-finance/commit/83095c9
[6e5c4fd]: https://github.com/hiqdev/hipanel-module-finance/commit/6e5c4fd
[eb8d63e]: https://github.com/hiqdev/hipanel-module-finance/commit/eb8d63e
[685eb2c]: https://github.com/hiqdev/hipanel-module-finance/commit/685eb2c
[c14a036]: https://github.com/hiqdev/hipanel-module-finance/commit/c14a036
[29a6c16]: https://github.com/hiqdev/hipanel-module-finance/commit/29a6c16
[588bf23]: https://github.com/hiqdev/hipanel-module-finance/commit/588bf23
[0f40b67]: https://github.com/hiqdev/hipanel-module-finance/commit/0f40b67
[0111b05]: https://github.com/hiqdev/hipanel-module-finance/commit/0111b05
[3e6a3f4]: https://github.com/hiqdev/hipanel-module-finance/commit/3e6a3f4
[93a3b00]: https://github.com/hiqdev/hipanel-module-finance/commit/93a3b00
[77975da]: https://github.com/hiqdev/hipanel-module-finance/commit/77975da
[2f999ab]: https://github.com/hiqdev/hipanel-module-finance/commit/2f999ab
[acb92e9]: https://github.com/hiqdev/hipanel-module-finance/commit/acb92e9
[0046d4a]: https://github.com/hiqdev/hipanel-module-finance/commit/0046d4a
[8ab4f5a]: https://github.com/hiqdev/hipanel-module-finance/commit/8ab4f5a
[611c2f0]: https://github.com/hiqdev/hipanel-module-finance/commit/611c2f0
[da1e45f]: https://github.com/hiqdev/hipanel-module-finance/commit/da1e45f
[325f428]: https://github.com/hiqdev/hipanel-module-finance/commit/325f428
[2d6d07f]: https://github.com/hiqdev/hipanel-module-finance/commit/2d6d07f
[bd4adc6]: https://github.com/hiqdev/hipanel-module-finance/commit/bd4adc6
[16232ea]: https://github.com/hiqdev/hipanel-module-finance/commit/16232ea
[eea9174]: https://github.com/hiqdev/hipanel-module-finance/commit/eea9174
[cb53d22]: https://github.com/hiqdev/hipanel-module-finance/commit/cb53d22
[a62c53e]: https://github.com/hiqdev/hipanel-module-finance/commit/a62c53e
[540d96d]: https://github.com/hiqdev/hipanel-module-finance/commit/540d96d
[ae001f6]: https://github.com/hiqdev/hipanel-module-finance/commit/ae001f6
[f34524c]: https://github.com/hiqdev/hipanel-module-finance/commit/f34524c
[5968e9b]: https://github.com/hiqdev/hipanel-module-finance/commit/5968e9b
[c95729a]: https://github.com/hiqdev/hipanel-module-finance/commit/c95729a
[981b5c2]: https://github.com/hiqdev/hipanel-module-finance/commit/981b5c2
[3628150]: https://github.com/hiqdev/hipanel-module-finance/commit/3628150
[d56ff71]: https://github.com/hiqdev/hipanel-module-finance/commit/d56ff71
[ef409ea]: https://github.com/hiqdev/hipanel-module-finance/commit/ef409ea
[d193959]: https://github.com/hiqdev/hipanel-module-finance/commit/d193959
[bd18338]: https://github.com/hiqdev/hipanel-module-finance/commit/bd18338
[5f0a2a4]: https://github.com/hiqdev/hipanel-module-finance/commit/5f0a2a4
[604808a]: https://github.com/hiqdev/hipanel-module-finance/commit/604808a
[fae771a]: https://github.com/hiqdev/hipanel-module-finance/commit/fae771a
[d4c6d86]: https://github.com/hiqdev/hipanel-module-finance/commit/d4c6d86
[7c6259c]: https://github.com/hiqdev/hipanel-module-finance/commit/7c6259c
[5ed3415]: https://github.com/hiqdev/hipanel-module-finance/commit/5ed3415
[50883e0]: https://github.com/hiqdev/hipanel-module-finance/commit/50883e0
[4649c23]: https://github.com/hiqdev/hipanel-module-finance/commit/4649c23
[2220972]: https://github.com/hiqdev/hipanel-module-finance/commit/2220972
[960f744]: https://github.com/hiqdev/hipanel-module-finance/commit/960f744
[c0bc083]: https://github.com/hiqdev/hipanel-module-finance/commit/c0bc083
[ac5a8e5]: https://github.com/hiqdev/hipanel-module-finance/commit/ac5a8e5
[96d7fc3]: https://github.com/hiqdev/hipanel-module-finance/commit/96d7fc3
[9f7b81c]: https://github.com/hiqdev/hipanel-module-finance/commit/9f7b81c
[6c6aebd]: https://github.com/hiqdev/hipanel-module-finance/commit/6c6aebd
[486e058]: https://github.com/hiqdev/hipanel-module-finance/commit/486e058
[8cc5d70]: https://github.com/hiqdev/hipanel-module-finance/commit/8cc5d70
[c58bee9]: https://github.com/hiqdev/hipanel-module-finance/commit/c58bee9
[15aaab0]: https://github.com/hiqdev/hipanel-module-finance/commit/15aaab0
[bf855b5]: https://github.com/hiqdev/hipanel-module-finance/commit/bf855b5
[7dfe5af]: https://github.com/hiqdev/hipanel-module-finance/commit/7dfe5af
[1929274]: https://github.com/hiqdev/hipanel-module-finance/commit/1929274
[9c032a6]: https://github.com/hiqdev/hipanel-module-finance/commit/9c032a6
[8b8bbfe]: https://github.com/hiqdev/hipanel-module-finance/commit/8b8bbfe
[57baa40]: https://github.com/hiqdev/hipanel-module-finance/commit/57baa40
[08216ec]: https://github.com/hiqdev/hipanel-module-finance/commit/08216ec
[d0b12d1]: https://github.com/hiqdev/hipanel-module-finance/commit/d0b12d1
[5145a10]: https://github.com/hiqdev/hipanel-module-finance/commit/5145a10
[e1b936f]: https://github.com/hiqdev/hipanel-module-finance/commit/e1b936f
[7b423b5]: https://github.com/hiqdev/hipanel-module-finance/commit/7b423b5
[0347d17]: https://github.com/hiqdev/hipanel-module-finance/commit/0347d17
[d3dc841]: https://github.com/hiqdev/hipanel-module-finance/commit/d3dc841
[59020bb]: https://github.com/hiqdev/hipanel-module-finance/commit/59020bb
[6799146]: https://github.com/hiqdev/hipanel-module-finance/commit/6799146
[f2142e6]: https://github.com/hiqdev/hipanel-module-finance/commit/f2142e6
[75f5355]: https://github.com/hiqdev/hipanel-module-finance/commit/75f5355
[8102422]: https://github.com/hiqdev/hipanel-module-finance/commit/8102422
[6695253]: https://github.com/hiqdev/hipanel-module-finance/commit/6695253
[d41091b]: https://github.com/hiqdev/hipanel-module-finance/commit/d41091b
[8877ca8]: https://github.com/hiqdev/hipanel-module-finance/commit/8877ca8
[78cd0cf]: https://github.com/hiqdev/hipanel-module-finance/commit/78cd0cf
[b638fc5]: https://github.com/hiqdev/hipanel-module-finance/commit/b638fc5
[f4a9d42]: https://github.com/hiqdev/hipanel-module-finance/commit/f4a9d42
[d48a7f0]: https://github.com/hiqdev/hipanel-module-finance/commit/d48a7f0
[746e4b5]: https://github.com/hiqdev/hipanel-module-finance/commit/746e4b5
[934bf1d]: https://github.com/hiqdev/hipanel-module-finance/commit/934bf1d
[c61d2bb]: https://github.com/hiqdev/hipanel-module-finance/commit/c61d2bb
[41497e0]: https://github.com/hiqdev/hipanel-module-finance/commit/41497e0
[e8b32d5]: https://github.com/hiqdev/hipanel-module-finance/commit/e8b32d5
[f7d790c]: https://github.com/hiqdev/hipanel-module-finance/commit/f7d790c
[6036feb]: https://github.com/hiqdev/hipanel-module-finance/commit/6036feb
[c72fcfe]: https://github.com/hiqdev/hipanel-module-finance/commit/c72fcfe
[834dc74]: https://github.com/hiqdev/hipanel-module-finance/commit/834dc74
[9f3f4c0]: https://github.com/hiqdev/hipanel-module-finance/commit/9f3f4c0
[06508d7]: https://github.com/hiqdev/hipanel-module-finance/commit/06508d7
[3142ade]: https://github.com/hiqdev/hipanel-module-finance/commit/3142ade
[c120775]: https://github.com/hiqdev/hipanel-module-finance/commit/c120775
[cf0c774]: https://github.com/hiqdev/hipanel-module-finance/commit/cf0c774
[553df88]: https://github.com/hiqdev/hipanel-module-finance/commit/553df88
[62dc0f9]: https://github.com/hiqdev/hipanel-module-finance/commit/62dc0f9
[099e33e]: https://github.com/hiqdev/hipanel-module-finance/commit/099e33e
[b535da4]: https://github.com/hiqdev/hipanel-module-finance/commit/b535da4
[f33ee1f]: https://github.com/hiqdev/hipanel-module-finance/commit/f33ee1f
[8301f0f]: https://github.com/hiqdev/hipanel-module-finance/commit/8301f0f
[d3fa24b]: https://github.com/hiqdev/hipanel-module-finance/commit/d3fa24b
[0a36716]: https://github.com/hiqdev/hipanel-module-finance/commit/0a36716
[1d2fea7]: https://github.com/hiqdev/hipanel-module-finance/commit/1d2fea7
[1be3f66]: https://github.com/hiqdev/hipanel-module-finance/commit/1be3f66
[136e869]: https://github.com/hiqdev/hipanel-module-finance/commit/136e869
[2bc2ecb]: https://github.com/hiqdev/hipanel-module-finance/commit/2bc2ecb
[8d087f0]: https://github.com/hiqdev/hipanel-module-finance/commit/8d087f0
[bc0843f]: https://github.com/hiqdev/hipanel-module-finance/commit/bc0843f
[137830f]: https://github.com/hiqdev/hipanel-module-finance/commit/137830f
[28b78f5]: https://github.com/hiqdev/hipanel-module-finance/commit/28b78f5
[eba92de]: https://github.com/hiqdev/hipanel-module-finance/commit/eba92de
[8e9a86f]: https://github.com/hiqdev/hipanel-module-finance/commit/8e9a86f
[e371deb]: https://github.com/hiqdev/hipanel-module-finance/commit/e371deb
[1993b92]: https://github.com/hiqdev/hipanel-module-finance/commit/1993b92
[62d2ea6]: https://github.com/hiqdev/hipanel-module-finance/commit/62d2ea6
[880a00e]: https://github.com/hiqdev/hipanel-module-finance/commit/880a00e
[e007e25]: https://github.com/hiqdev/hipanel-module-finance/commit/e007e25
[68dd9c8]: https://github.com/hiqdev/hipanel-module-finance/commit/68dd9c8
[f6c8da6]: https://github.com/hiqdev/hipanel-module-finance/commit/f6c8da6
[2435eaf]: https://github.com/hiqdev/hipanel-module-finance/commit/2435eaf
[efdf8fe]: https://github.com/hiqdev/hipanel-module-finance/commit/efdf8fe
[2036d74]: https://github.com/hiqdev/hipanel-module-finance/commit/2036d74
[3d3abc6]: https://github.com/hiqdev/hipanel-module-finance/commit/3d3abc6
[2ed2060]: https://github.com/hiqdev/hipanel-module-finance/commit/2ed2060
[2631265]: https://github.com/hiqdev/hipanel-module-finance/commit/2631265
[b6c3859]: https://github.com/hiqdev/hipanel-module-finance/commit/b6c3859
[c8d9709]: https://github.com/hiqdev/hipanel-module-finance/commit/c8d9709
[b8a38aa]: https://github.com/hiqdev/hipanel-module-finance/commit/b8a38aa
[f9920d1]: https://github.com/hiqdev/hipanel-module-finance/commit/f9920d1
[74502bd]: https://github.com/hiqdev/hipanel-module-finance/commit/74502bd
[95317ce]: https://github.com/hiqdev/hipanel-module-finance/commit/95317ce
[fa2818e]: https://github.com/hiqdev/hipanel-module-finance/commit/fa2818e
[167b818]: https://github.com/hiqdev/hipanel-module-finance/commit/167b818
[e9fc156]: https://github.com/hiqdev/hipanel-module-finance/commit/e9fc156
[84ac6db]: https://github.com/hiqdev/hipanel-module-finance/commit/84ac6db
[2e1537d]: https://github.com/hiqdev/hipanel-module-finance/commit/2e1537d
[04f67e3]: https://github.com/hiqdev/hipanel-module-finance/commit/04f67e3
[9826ce9]: https://github.com/hiqdev/hipanel-module-finance/commit/9826ce9
[0e06282]: https://github.com/hiqdev/hipanel-module-finance/commit/0e06282
[bf211c8]: https://github.com/hiqdev/hipanel-module-finance/commit/bf211c8
[1f91f11]: https://github.com/hiqdev/hipanel-module-finance/commit/1f91f11
[3c00bba]: https://github.com/hiqdev/hipanel-module-finance/commit/3c00bba
[abb0784]: https://github.com/hiqdev/hipanel-module-finance/commit/abb0784
[9732961]: https://github.com/hiqdev/hipanel-module-finance/commit/9732961
[327bbda]: https://github.com/hiqdev/hipanel-module-finance/commit/327bbda
[c3bb5ee]: https://github.com/hiqdev/hipanel-module-finance/commit/c3bb5ee
[0a64ef4]: https://github.com/hiqdev/hipanel-module-finance/commit/0a64ef4
[ec26015]: https://github.com/hiqdev/hipanel-module-finance/commit/ec26015
[74a8abd]: https://github.com/hiqdev/hipanel-module-finance/commit/74a8abd
[1ba8e64]: https://github.com/hiqdev/hipanel-module-finance/commit/1ba8e64
[12261e0]: https://github.com/hiqdev/hipanel-module-finance/commit/12261e0
[8cc55ef]: https://github.com/hiqdev/hipanel-module-finance/commit/8cc55ef
[15775ce]: https://github.com/hiqdev/hipanel-module-finance/commit/15775ce
[2225324]: https://github.com/hiqdev/hipanel-module-finance/commit/2225324
[b34cc4d]: https://github.com/hiqdev/hipanel-module-finance/commit/b34cc4d
[4c33eb7]: https://github.com/hiqdev/hipanel-module-finance/commit/4c33eb7
[5308a40]: https://github.com/hiqdev/hipanel-module-finance/commit/5308a40
[ee85930]: https://github.com/hiqdev/hipanel-module-finance/commit/ee85930
[3085624]: https://github.com/hiqdev/hipanel-module-finance/commit/3085624
[f0fcb7b]: https://github.com/hiqdev/hipanel-module-finance/commit/f0fcb7b
[34b53d4]: https://github.com/hiqdev/hipanel-module-finance/commit/34b53d4
[76d5968]: https://github.com/hiqdev/hipanel-module-finance/commit/76d5968
[7715b59]: https://github.com/hiqdev/hipanel-module-finance/commit/7715b59
[851765c]: https://github.com/hiqdev/hipanel-module-finance/commit/851765c
[873c221]: https://github.com/hiqdev/hipanel-module-finance/commit/873c221
[0272daf]: https://github.com/hiqdev/hipanel-module-finance/commit/0272daf
[ed433d4]: https://github.com/hiqdev/hipanel-module-finance/commit/ed433d4
[bb26cdd]: https://github.com/hiqdev/hipanel-module-finance/commit/bb26cdd
[a45d972]: https://github.com/hiqdev/hipanel-module-finance/commit/a45d972
[ab7c287]: https://github.com/hiqdev/hipanel-module-finance/commit/ab7c287
[546d988]: https://github.com/hiqdev/hipanel-module-finance/commit/546d988
[aee4750]: https://github.com/hiqdev/hipanel-module-finance/commit/aee4750
[ad8d628]: https://github.com/hiqdev/hipanel-module-finance/commit/ad8d628
[497cca2]: https://github.com/hiqdev/hipanel-module-finance/commit/497cca2
[d60d1df]: https://github.com/hiqdev/hipanel-module-finance/commit/d60d1df
[706d380]: https://github.com/hiqdev/hipanel-module-finance/commit/706d380
[bffcbd1]: https://github.com/hiqdev/hipanel-module-finance/commit/bffcbd1
[117392d]: https://github.com/hiqdev/hipanel-module-finance/commit/117392d
[13ae642]: https://github.com/hiqdev/hipanel-module-finance/commit/13ae642
[f4eec0b]: https://github.com/hiqdev/hipanel-module-finance/commit/f4eec0b
[9b7eb74]: https://github.com/hiqdev/hipanel-module-finance/commit/9b7eb74
[7957530]: https://github.com/hiqdev/hipanel-module-finance/commit/7957530
[3770db4]: https://github.com/hiqdev/hipanel-module-finance/commit/3770db4
[5701cde]: https://github.com/hiqdev/hipanel-module-finance/commit/5701cde
[016bf91]: https://github.com/hiqdev/hipanel-module-finance/commit/016bf91
[30851eb]: https://github.com/hiqdev/hipanel-module-finance/commit/30851eb
[06e8c32]: https://github.com/hiqdev/hipanel-module-finance/commit/06e8c32
[ad5800c]: https://github.com/hiqdev/hipanel-module-finance/commit/ad5800c
[ad5f152]: https://github.com/hiqdev/hipanel-module-finance/commit/ad5f152
[0e217f1]: https://github.com/hiqdev/hipanel-module-finance/commit/0e217f1
[0b2f89c]: https://github.com/hiqdev/hipanel-module-finance/commit/0b2f89c
[20e8664]: https://github.com/hiqdev/hipanel-module-finance/commit/20e8664
[0b8c6eb]: https://github.com/hiqdev/hipanel-module-finance/commit/0b8c6eb
[67e3249]: https://github.com/hiqdev/hipanel-module-finance/commit/67e3249
[d71e56e]: https://github.com/hiqdev/hipanel-module-finance/commit/d71e56e
[e33def5]: https://github.com/hiqdev/hipanel-module-finance/commit/e33def5
[c87d76b]: https://github.com/hiqdev/hipanel-module-finance/commit/c87d76b
[7902940]: https://github.com/hiqdev/hipanel-module-finance/commit/7902940
[ebab548]: https://github.com/hiqdev/hipanel-module-finance/commit/ebab548
[4563290]: https://github.com/hiqdev/hipanel-module-finance/commit/4563290
[c692859]: https://github.com/hiqdev/hipanel-module-finance/commit/c692859
[37f66ff]: https://github.com/hiqdev/hipanel-module-finance/commit/37f66ff
[bf59688]: https://github.com/hiqdev/hipanel-module-finance/commit/bf59688
[41674b1]: https://github.com/hiqdev/hipanel-module-finance/commit/41674b1
[14088ce]: https://github.com/hiqdev/hipanel-module-finance/commit/14088ce
[ef93622]: https://github.com/hiqdev/hipanel-module-finance/commit/ef93622
[f035973]: https://github.com/hiqdev/hipanel-module-finance/commit/f035973
[d3abd8c]: https://github.com/hiqdev/hipanel-module-finance/commit/d3abd8c
[75f5f12]: https://github.com/hiqdev/hipanel-module-finance/commit/75f5f12
[c24b485]: https://github.com/hiqdev/hipanel-module-finance/commit/c24b485
[71f168d]: https://github.com/hiqdev/hipanel-module-finance/commit/71f168d
[581abd9]: https://github.com/hiqdev/hipanel-module-finance/commit/581abd9
[aeff303]: https://github.com/hiqdev/hipanel-module-finance/commit/aeff303
[8c1c912]: https://github.com/hiqdev/hipanel-module-finance/commit/8c1c912
[8df6d6d]: https://github.com/hiqdev/hipanel-module-finance/commit/8df6d6d
[706dd85]: https://github.com/hiqdev/hipanel-module-finance/commit/706dd85
[39961d3]: https://github.com/hiqdev/hipanel-module-finance/commit/39961d3
[7a3efdc]: https://github.com/hiqdev/hipanel-module-finance/commit/7a3efdc
[4f3c1bc]: https://github.com/hiqdev/hipanel-module-finance/commit/4f3c1bc
[0d0c353]: https://github.com/hiqdev/hipanel-module-finance/commit/0d0c353
[7131b7a]: https://github.com/hiqdev/hipanel-module-finance/commit/7131b7a
[eb64f25]: https://github.com/hiqdev/hipanel-module-finance/commit/eb64f25
[e07b14b]: https://github.com/hiqdev/hipanel-module-finance/commit/e07b14b
[1df109c]: https://github.com/hiqdev/hipanel-module-finance/commit/1df109c
[72c3049]: https://github.com/hiqdev/hipanel-module-finance/commit/72c3049
[72ce260]: https://github.com/hiqdev/hipanel-module-finance/commit/72ce260
[7b333d0]: https://github.com/hiqdev/hipanel-module-finance/commit/7b333d0
[eb9bbd9]: https://github.com/hiqdev/hipanel-module-finance/commit/eb9bbd9
[e6d0475]: https://github.com/hiqdev/hipanel-module-finance/commit/e6d0475
[be07663]: https://github.com/hiqdev/hipanel-module-finance/commit/be07663
[a6b2c6c]: https://github.com/hiqdev/hipanel-module-finance/commit/a6b2c6c
[ed32789]: https://github.com/hiqdev/hipanel-module-finance/commit/ed32789
[d7a278b]: https://github.com/hiqdev/hipanel-module-finance/commit/d7a278b
[073839c]: https://github.com/hiqdev/hipanel-module-finance/commit/073839c
[6b97773]: https://github.com/hiqdev/hipanel-module-finance/commit/6b97773
[4b89d71]: https://github.com/hiqdev/hipanel-module-finance/commit/4b89d71
[e0392db]: https://github.com/hiqdev/hipanel-module-finance/commit/e0392db
[2d14d19]: https://github.com/hiqdev/hipanel-module-finance/commit/2d14d19
[cc37e0d]: https://github.com/hiqdev/hipanel-module-finance/commit/cc37e0d
[27fb148]: https://github.com/hiqdev/hipanel-module-finance/commit/27fb148
[6d11c76]: https://github.com/hiqdev/hipanel-module-finance/commit/6d11c76
[519d4a0]: https://github.com/hiqdev/hipanel-module-finance/commit/519d4a0
[9747b20]: https://github.com/hiqdev/hipanel-module-finance/commit/9747b20
[0211a32]: https://github.com/hiqdev/hipanel-module-finance/commit/0211a32
[96c4d74]: https://github.com/hiqdev/hipanel-module-finance/commit/96c4d74
[284371a]: https://github.com/hiqdev/hipanel-module-finance/commit/284371a
[1706a0a]: https://github.com/hiqdev/hipanel-module-finance/commit/1706a0a
[e3fded1]: https://github.com/hiqdev/hipanel-module-finance/commit/e3fded1
[22f970a]: https://github.com/hiqdev/hipanel-module-finance/commit/22f970a
[94b1399]: https://github.com/hiqdev/hipanel-module-finance/commit/94b1399
[91cf720]: https://github.com/hiqdev/hipanel-module-finance/commit/91cf720
[d4aa727]: https://github.com/hiqdev/hipanel-module-finance/commit/d4aa727
[cd215e0]: https://github.com/hiqdev/hipanel-module-finance/commit/cd215e0
[73585bf]: https://github.com/hiqdev/hipanel-module-finance/commit/73585bf
[109ed86]: https://github.com/hiqdev/hipanel-module-finance/commit/109ed86
[f5d3631]: https://github.com/hiqdev/hipanel-module-finance/commit/f5d3631
[dc8c4b6]: https://github.com/hiqdev/hipanel-module-finance/commit/dc8c4b6
[75f2505]: https://github.com/hiqdev/hipanel-module-finance/commit/75f2505
[0a09008]: https://github.com/hiqdev/hipanel-module-finance/commit/0a09008
[e0553ed]: https://github.com/hiqdev/hipanel-module-finance/commit/e0553ed
[c96eebb]: https://github.com/hiqdev/hipanel-module-finance/commit/c96eebb
[d9c24b5]: https://github.com/hiqdev/hipanel-module-finance/commit/d9c24b5
[e926398]: https://github.com/hiqdev/hipanel-module-finance/commit/e926398
[f6b39d9]: https://github.com/hiqdev/hipanel-module-finance/commit/f6b39d9
[9c007bc]: https://github.com/hiqdev/hipanel-module-finance/commit/9c007bc
[1cb7117]: https://github.com/hiqdev/hipanel-module-finance/commit/1cb7117
[ec41910]: https://github.com/hiqdev/hipanel-module-finance/commit/ec41910
[0b05637]: https://github.com/hiqdev/hipanel-module-finance/commit/0b05637
[ecb3c3e]: https://github.com/hiqdev/hipanel-module-finance/commit/ecb3c3e
[b074591]: https://github.com/hiqdev/hipanel-module-finance/commit/b074591
[7f3712e]: https://github.com/hiqdev/hipanel-module-finance/commit/7f3712e
[dc93ab3]: https://github.com/hiqdev/hipanel-module-finance/commit/dc93ab3
[77b1bbe]: https://github.com/hiqdev/hipanel-module-finance/commit/77b1bbe
[bf466a7]: https://github.com/hiqdev/hipanel-module-finance/commit/bf466a7
[5b6f419]: https://github.com/hiqdev/hipanel-module-finance/commit/5b6f419
[5a5e4be]: https://github.com/hiqdev/hipanel-module-finance/commit/5a5e4be
[8351d4b]: https://github.com/hiqdev/hipanel-module-finance/commit/8351d4b
[91e0faa]: https://github.com/hiqdev/hipanel-module-finance/commit/91e0faa
[3ae07e0]: https://github.com/hiqdev/hipanel-module-finance/commit/3ae07e0
[707314b]: https://github.com/hiqdev/hipanel-module-finance/commit/707314b
[7d30b9e]: https://github.com/hiqdev/hipanel-module-finance/commit/7d30b9e
[b87a46b]: https://github.com/hiqdev/hipanel-module-finance/commit/b87a46b
[018a2f4]: https://github.com/hiqdev/hipanel-module-finance/commit/018a2f4
[0622f1d]: https://github.com/hiqdev/hipanel-module-finance/commit/0622f1d
[ef2c0cc]: https://github.com/hiqdev/hipanel-module-finance/commit/ef2c0cc
[83485fb]: https://github.com/hiqdev/hipanel-module-finance/commit/83485fb
[0edfe96]: https://github.com/hiqdev/hipanel-module-finance/commit/0edfe96
[7164295]: https://github.com/hiqdev/hipanel-module-finance/commit/7164295
[b2cff45]: https://github.com/hiqdev/hipanel-module-finance/commit/b2cff45
[e3a0660]: https://github.com/hiqdev/hipanel-module-finance/commit/e3a0660
[694418f]: https://github.com/hiqdev/hipanel-module-finance/commit/694418f
[89eb044]: https://github.com/hiqdev/hipanel-module-finance/commit/89eb044
[dad9027]: https://github.com/hiqdev/hipanel-module-finance/commit/dad9027
[030ae6c]: https://github.com/hiqdev/hipanel-module-finance/commit/030ae6c
[844c418]: https://github.com/hiqdev/hipanel-module-finance/commit/844c418
[83d0f02]: https://github.com/hiqdev/hipanel-module-finance/commit/83d0f02
[0998523]: https://github.com/hiqdev/hipanel-module-finance/commit/0998523
[0012a2c]: https://github.com/hiqdev/hipanel-module-finance/commit/0012a2c
[35273b6]: https://github.com/hiqdev/hipanel-module-finance/commit/35273b6
[ef5cdd9]: https://github.com/hiqdev/hipanel-module-finance/commit/ef5cdd9
[ffdb66e]: https://github.com/hiqdev/hipanel-module-finance/commit/ffdb66e
[14391bd]: https://github.com/hiqdev/hipanel-module-finance/commit/14391bd
[3e99904]: https://github.com/hiqdev/hipanel-module-finance/commit/3e99904
[5358194]: https://github.com/hiqdev/hipanel-module-finance/commit/5358194
[ac2069c]: https://github.com/hiqdev/hipanel-module-finance/commit/ac2069c
[44a8327]: https://github.com/hiqdev/hipanel-module-finance/commit/44a8327
[3cbdc11]: https://github.com/hiqdev/hipanel-module-finance/commit/3cbdc11
[d3dc0ea]: https://github.com/hiqdev/hipanel-module-finance/commit/d3dc0ea
[09d5c87]: https://github.com/hiqdev/hipanel-module-finance/commit/09d5c87
[7d2c3ca]: https://github.com/hiqdev/hipanel-module-finance/commit/7d2c3ca
[c168d11]: https://github.com/hiqdev/hipanel-module-finance/commit/c168d11
[5ea29a4]: https://github.com/hiqdev/hipanel-module-finance/commit/5ea29a4
[8207821]: https://github.com/hiqdev/hipanel-module-finance/commit/8207821
[28f1fc7]: https://github.com/hiqdev/hipanel-module-finance/commit/28f1fc7
[ab5227b]: https://github.com/hiqdev/hipanel-module-finance/commit/ab5227b
[374da43]: https://github.com/hiqdev/hipanel-module-finance/commit/374da43
[5f375d3]: https://github.com/hiqdev/hipanel-module-finance/commit/5f375d3
[23d4574]: https://github.com/hiqdev/hipanel-module-finance/commit/23d4574
[52f95e3]: https://github.com/hiqdev/hipanel-module-finance/commit/52f95e3
[e68327d]: https://github.com/hiqdev/hipanel-module-finance/commit/e68327d
[c2b6e42]: https://github.com/hiqdev/hipanel-module-finance/commit/c2b6e42
[d19dbfb]: https://github.com/hiqdev/hipanel-module-finance/commit/d19dbfb
[cc9eb10]: https://github.com/hiqdev/hipanel-module-finance/commit/cc9eb10
[ee2c76a]: https://github.com/hiqdev/hipanel-module-finance/commit/ee2c76a
[aceb9ca]: https://github.com/hiqdev/hipanel-module-finance/commit/aceb9ca
[b6c142a]: https://github.com/hiqdev/hipanel-module-finance/commit/b6c142a
[5de8396]: https://github.com/hiqdev/hipanel-module-finance/commit/5de8396
[d57e692]: https://github.com/hiqdev/hipanel-module-finance/commit/d57e692
[1e3482a]: https://github.com/hiqdev/hipanel-module-finance/commit/1e3482a
[8ae4c5d]: https://github.com/hiqdev/hipanel-module-finance/commit/8ae4c5d
[071be19]: https://github.com/hiqdev/hipanel-module-finance/commit/071be19
[033e7e4]: https://github.com/hiqdev/hipanel-module-finance/commit/033e7e4
[f793c97]: https://github.com/hiqdev/hipanel-module-finance/commit/f793c97
[5280b4b]: https://github.com/hiqdev/hipanel-module-finance/commit/5280b4b
[b139aad]: https://github.com/hiqdev/hipanel-module-finance/commit/b139aad
[670e60c]: https://github.com/hiqdev/hipanel-module-finance/commit/670e60c
[907d4ee]: https://github.com/hiqdev/hipanel-module-finance/commit/907d4ee
[13f02fb]: https://github.com/hiqdev/hipanel-module-finance/commit/13f02fb
[a43a895]: https://github.com/hiqdev/hipanel-module-finance/commit/a43a895
[64b5f68]: https://github.com/hiqdev/hipanel-module-finance/commit/64b5f68
[9399360]: https://github.com/hiqdev/hipanel-module-finance/commit/9399360
[d785b73]: https://github.com/hiqdev/hipanel-module-finance/commit/d785b73
[92caf2f]: https://github.com/hiqdev/hipanel-module-finance/commit/92caf2f
[a3bb73c]: https://github.com/hiqdev/hipanel-module-finance/commit/a3bb73c
[cd20091]: https://github.com/hiqdev/hipanel-module-finance/commit/cd20091
[0960768]: https://github.com/hiqdev/hipanel-module-finance/commit/0960768
[436f24b]: https://github.com/hiqdev/hipanel-module-finance/commit/436f24b
[0017b4d]: https://github.com/hiqdev/hipanel-module-finance/commit/0017b4d
[6eefbd4]: https://github.com/hiqdev/hipanel-module-finance/commit/6eefbd4
[3ece88a]: https://github.com/hiqdev/hipanel-module-finance/commit/3ece88a
[1fac83b]: https://github.com/hiqdev/hipanel-module-finance/commit/1fac83b
[0322f0d]: https://github.com/hiqdev/hipanel-module-finance/commit/0322f0d
[c289c75]: https://github.com/hiqdev/hipanel-module-finance/commit/c289c75
[899f594]: https://github.com/hiqdev/hipanel-module-finance/commit/899f594
[96de292]: https://github.com/hiqdev/hipanel-module-finance/commit/96de292
[1fbcee9]: https://github.com/hiqdev/hipanel-module-finance/commit/1fbcee9
[Under development]: https://github.com/hiqdev/hipanel-module-finance/releases
[Under]: https://github.com/hiqdev/hipanel-module-finance/releases/tag/Under
