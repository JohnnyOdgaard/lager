# lager

## Adgang via subdomæne

Samme kode kan bruges fra `http://localhost/lager/` og fra roden af
`https://lager.iodata.dk/`. På subdomænet skal sitets fysiske rod pege på
lager-mappen; lokalt ligger den under webrodens `lager`-mappe.

`includes/url.php` danner installationsrelative URL'er ud fra det kørende
scripts filsti og `SCRIPT_NAME`. Links, formularer, viderestillinger og
statiske filer får dermed automatisk `/lager`-præfiks lokalt og intet
præfiks på subdomænet. Relative links mellem moduler bevares. Det kræver
ingen ændring af `includes/config.php` eller databaseforbindelsen.
Adgangsbegrænsning eller viderestilling fra den gamle adresse
`iodata.dk/lager/` håndteres fortsat i serveropsætningen.

## Eksport af lagerbeholdning

Kortet "Eksportér lagerbeholdning" på forsiden gemmer alle lagerposter i
`csv/lagebeholdning.csv` under lager-sitets rod. Mappen oprettes automatisk,
og webserveren skal have skriverettigheder til den.

Første linje indeholder `emne;Enhed;Placering;maengde;kobsdato;Sidstedato`
til brug i P-Touch. Kolonnenavnene Enhed og Placering er uændrede.
Filen bruger semikolon og UTF-8 med BOM til Excel. Enhed og placering
eksporteres som navne, og datoer og mængder bevarer databaseværdierne.
Lagerposterne sorteres efter Købt med nyeste dato først; ved samme dato
sorteres efter varenavn og ID. Købsdatoer i både YYYY-MM-DD og DD-MM-YYYY
understøttes, og poster uden købsdato placeres til sidst.
Hver eksport erstatter den tidligere fil, når hele den nye fil er skrevet.
Forsiden viser en bekræftelse med downloadlink eller en fejlmeddelelse.

Den eksisterende CSV-download i lagermodulet er uændret.

## Bestilling efter udtagning

[Hent via navn/kategori](./modules/hent/hent_advanced.php) viser kun
lagerposter med en mængde større end nul. Mængden vises uden decimaler,
efterfulgt af kolonnen **Enhed**.
Når du tømmer en lagerpost og vælger **Opret bestilling**, kommer du
tilbage til søgningen, hvis der ikke er varer markeret som købt.
Ellers vises de købte varer som før.

## Transaktioner

Sammenhørende ændringer i indkøb, udtagning, bestillingssletning,
arkivering, CSV-import, masseændringer og fletning af enheder, kategorier
og placeringer udføres i en databasetransaktion. Hvis en databaseændring
fejler, rulles hele den pågældende handling tilbage. De involverede
tabeller skal bruge InnoDB. En transaktion erstatter ikke backup og kan
ikke fortryde en allerede gennemført handling.
