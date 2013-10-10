ALE
===

Základ valné většiny modulů / komponent a knihoven.


Instalace
---------

1. Stažení přes composer
2. Připojení DI rozšíření Ale\DI\AleExtension

> Pokud nevíte jakým způsobem připojit rošíření, dopoučuji použít rošíření https://github.com/vojtech-dobes/nette-extensions-list, které umožňuje následně v konfiguračním souboru definovat sekci "extensions", kde pak lze jednoduše přidat toto rošíření. Alternativně je nutné v boostrapu v události onCompile na Configuration zavěsit callback, který bude přidávat všechna Vaše rožšíření pomocí volání metody addExtension na Compiler.


Konfigurace
-----------

Knihovna vyžaduje balíček Flame/modules, který je nutné mít správně nainstalovaný a nakonfigurovaný v boostrapu, viz
https://github.com/flame-org/Modules.



Základní entity
---------------

Knihovna obsahuje 3 základní entity (BaseEntity, IdentifiedEntity, NamedEntity), od BaseEntity by měli dědit všechny entity aplikace (IdentifiedEntity a NamedEntity dědí též) a jedná se o základní entitu, která zapouzdřuje funkčnost z Kdyby/Doctrine, a používá se při práci s ostatními knihovnami (například DataExt, EntityMetaReader, a další).



Ale\Application\UI\Presenter
----------------------------

Objekt rozšiřující možnosti Nette presenteru, může být použit jako základní presenter pro všechny Vaše presentery. Tento presenter přidává následující funkční celky:

- Autowiring služeb namísto používání inject* metod, viz https://github.com/Kdyby/Autowired/blob/master/src/Kdyby/Autowired/AutowireProperties.php

- Autowiring továrniček viz https://github.com/Kdyby/Autowired/blob/master/src/Kdyby/Autowired/AutowireComponentFactories.php

- Autowiring repozitářů skrz továrničku

- Přijímání entit v action http://forum.nette.org/cs/13568-router-vracia-objekty-entity-namiesto-skalarov#p102228


```php
class TestPresenter extends Ale\Application\UI\Presenter
{

	/**
	 * @autowire
	 * @var Foo
	 */
	public $foo;


	/**
	 * @autowire(factory="Ale\DaoFactory", \App\User)
	 * @var \Kdyby\Doctrine\EntityDao
	 */
	public $userRepository;


    public function actionDefault(User $user, Shop $shop = NULL)
    {
        // příklad získání dané entity pomocí primárního klíče
        // Například {plink Test:default, user => 1, shop => 2}
        var_dump($user); // Entita User s id 1
        var_dump($shop); // Entita Shop s id 2
    }


    public function renderDefault(User $user)
    {
        ...
    }


    public function renderDefault(User $user)
    {
        ...
    }


    /**
      * @return GridoExt\Grido
      */
    protected function createComponentDatagrid($name, IGridoFactory $factory)
    {
    	return $factory->create();
    }

}

```


Autowiring repozitářů v modelech
--------------------------------

Používáte-li často techniku, kdy si předáváte \Kdyby\Doctrine\EntityDao do modelů, tak jistě v konfigu používáte továrničku @dao.doctrine(Nejaka\Moje\Entita). Mě tohle moc nebaví, zvláště, když předávám například už 3 repozitáře v ten řádek konfigu se mi nafoukne. Ve finále je to skoro jediné, co se musí definovat, jinak autowiring se postará o vše ostatní.

Tohle jsem nakonec vyřešil s tím, že v anotaci služby si definujete název té entity a rozšířený autowiring se postará o ostatní, například:


```php
class MojeSluzba {

	/**
	 * @var EntityDao Nejaka\Moje\Entita
	 */
	public function __constructor($dao)
	{
	  ...
	}


}
```


Reponses
--------

V knihovně jsou obsaženy nové typy odpovědí server:

- ImageResponse - slouží k odeslání obrázku, využitelné například pro QR kódy apod.
- RedirectPostResponse - odeslání POST dat na danou URL adresu - funguje tak, že vrátí uživateli stránku s vygenerovaným formulářem, a danými POST daty (ve skrytých inputech), a automaticky pomocí JS (či kliknutím na vygenerované tlačítko) odešle tyto elementy na danou URL.
- JsonpResponse - odpověď pro JSONP požadavky