# Абстрактный фабричный класс для контейнеризации сервисов в Laravel и Symfony

## Инструкция

# Установка:

`composer require wolfcharaa/abstract_factory`

**Использование:**

Для того, чтобы использовать данный инструмент необходимо создать в папке с классами пустой класс ExampleFactory и отнаследоваться от AbstractFactory. Вот и всё, папка с классами добавлена в контейнер. Для дальнейшей инъекции класса необходимо вызвать в любом контроллере/сервисе/команде ExampleFactory, вызвать у неё метод create и передать в него названия класса или ClassName::class из папки с ExampleFactory

**Пример:**

Есть папки с отчётами и импортами:

_Service \
----Report \
--------ReportFactory.php \
--------MyReport.php \
--------YourReport.php \
--------SolaryReport.php \
----Import \
--------ImportFactory.php \
--------OwnersImport.php \
--------BuhImport.php_

Как видно выше, в каждой папке есть Factory класс.

Он не имеет в себе кода, и выглядит так для папки Report

```
<?php

namespace App\Service\Report;

use ElsaLib\AbstractFactory;

class ReportFactory extends AbstractFactory
{

}
```

Для подключения любого из reports в контроллер, в необходимый метод подаётся ReportFactory и вызывается метод create

```
class ReportController extends Controller
{
    /*.............*/
    public function createReport(Request $request, ReportFactory $reportFactory): JsonResponse
        {            
            $report = $reportFactory->create('MyReport');
            /* Или же таким способом */
            //$report = $reportFactory->create(MyReport::class);           
            $result = $report->someReportMethod();            
            return new JsonResponse(
                [                    
                    "report_result"    => $result,
                ]
            );
        }
}
```

Для удобства работы с методами инстанцированных классов, можно сделать интерфейс или абстрактный класс для группы (папки) классов, и явно указывать его при результате метода create (!Внимание, не забудьте имплементировать интерфейс или унаследоваться от абстрактного класса!)
Таким образом код выше поменяется до такого вида

```
class ReportController extends Controller
{
    /*.............*/
    public function createReport(Request $request, ReportFactory $reportFactory): JsonResponse
        {                        
            /** @var ReportInterface $report */
            $report = $reportFactory->create('MyReport');        
            $result = $report->someReportMethod();            
            return new JsonResponse(
                [                    
                    "report_result"    => $result,
                ]
            );
        }
}
```