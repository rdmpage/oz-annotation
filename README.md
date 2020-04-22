# Annotating PDFs

## Makefiles

- [Practical Makefiles, by example](http://nuclear.mutantstargoat.com/articles/make/)
- [Orchestrating batch processing pipelines with cron and make](https://snowplowanalytics.com/blog/2015/10/13/orchestrating-batch-processing-pipelines-with-cron-and-make/)
- [Decomplected workflows: Makefiles](https://web.archive.org/web/20150206054212/http://www.bioinformaticszen.com/post/decomplected-workflows-makefiles/)
- [makefile2dot](https://github.com/vak/makefile2dot)

## Pipelines

- pdf to xml
- xml to html (text only)

## Big HTML files and memory

```
php -c php.ini htmlMarkup.php pk.html > pkm.html
```

## Comparing to hypothes.is

Ideally we can exactly match annotation locators in hypothes.is with those I derive.

A test case is http://direct.biostor.org/p.html which is a simple HTML file. Annotations can be viewed [here](https://via.hypothes.is/http://direct.biostor.org/p.html). The first annotation is https://hypothes.is/api/annotations/sdsZbIMKEeqZjD-qAFuUOQ 

```
selector: [
{
type: "RangeSelector",
endOffset: 202,
startOffset: 192,
endContainer: "/p[46]",
startContainer: "/p[46]"
},
{
end: 19193,
type: "TextPositionSelector",
start: 19183
},
{
type: "TextQuoteSelector",
exact: "K001193666",
prefix: "\nfl.\n&amp;\nfr.\nDennis\n2017\n(holo\nK\n[",
suffix: "];\niso\nE\n[E00259853]).\nFig.\n1.\nh"
}
]

```

The TextQuote selector seems to work on raw text (in this case, the raw text without tags (e.g., italics). The RangeSelector uses cleaned text within a p element, and TextPositionSelector is position within cleaned text for the whole document.

### Examples to use with hypothesis.is

#### J Mammalogy
https://academic.oup.com/jmammal/article/89/4/815/868809 https://doi.org/10.1644/07-MAMM-A-285.1 nice example of a species description available as HTML.


#### CUP Edinburgh Journal of Botany

https://www.cambridge.org/core/journals/edinburgh-journal-of-botany/article/new-species-of-eriocaulon-eriocaulaceae-from-the-southern-western-ghats-of-kerala-india/AD5983CC30B0A9192BD08CF62BBAAC6C/core-reader
chrome-extension://bjfhmglciegochdpefhhlphglcehbmek/pdfjs/web/viewer.html?file=https%3A%2F%2Fwww.cambridge.org%2Fcore%2Fservices%2Faop-cambridge-core%2Fcontent%2Fview%2FAD5983CC30B0A9192BD08CF62BBAAC6C%2FS0960428620000013a.pdf%2Fdiv-class-title-a-new-species-of-span-class-italic-eriocaulon-span-eriocaulaceae-from-the-southern-western-ghats-of-kerala-india-div.pdf

Hypothes.is places annotation on both PDF and HTML

https://hypothes.is/api/annotations/BJKGEIPbEeqUhY9L5jQFJA
https://hypothes.is/api/annotations/CQYiCoPcEeqDvidrTZ_O9g

Note the HTML page has ```citation_pdf_url``` the same as the PDF being annotated.

```
<meta name="citation_pdf_url" content="https://www.cambridge.org/core/services/aop-cambridge-core/content/view/AD5983CC30B0A9192BD08CF62BBAAC6C/S0960428620000013a.pdf/div-class-title-a-new-species-of-span-class-italic-eriocaulon-span-eriocaulaceae-from-the-southern-western-ghats-of-kerala-india-div.pdf">
```

#### A New Species of Desmalopex 

https://academic.oup.com/jmammal/article/89/4/815/868809

chrome-extension://bjfhmglciegochdpefhhlphglcehbmek/pdfjs/web/viewer.html?file=https%3A%2F%2Fwatermark.silverchair.com%2F89-4-815.pdf%3Ftoken%3DAQECAHi208BE49Ooan9kkhW_Ercy7Dm3ZL_9Cf3qfKAc485ysgAAAmEwggJdBgkqhkiG9w0BBwagggJOMIICSgIBADCCAkMGCSqGSIb3DQEHATAeBglghkgBZQMEAS4wEQQMRCOyYdXZGwW8EOk5AgEQgIICFEfbD5WCNHLMPD1_RLdUsfYqR9W6s8PJDFWih81oZ0R271DNCLygz6BQ9BqOK-vfCzUe0V45Zhwc-8XFPTqaPM3ViHpbazBGy1Jt67-Iz-Lnz-WD1Y8194XGJ90F50QOjoPRSBmD3SMiZGCNdFQacuV5RmNvXBx6YBXqMaHzXAfVGqThoaA5VxMTV48zMuOVrv7DnLEhEgBMBOlUxOgpGtBKZr5BTPa6Lp5SKP5j_kf_dX-nvWjY1mWbzDSYOFCWnqpz4bUgjfGCW74Am0vJYOK61Kw7Av92zkyjaabibTCKQbL4Gjqpf1Zc5jMQNuXQNQpIf-itierQ_ucvYcu0ThT8rgBQOXaMqeMcQa57ZaV2WBkC4gjmG91ns3ksSqz3PW_Xy9X0Zwtp_ZejbQeFEnit3jsC8kWxpr6D3-q7Q1Bsh3PETIcS-qDnI3t6tsFA1rKgP93UH8UB54j-PKH-Bj4mcEwwEW5zX-mnxLlnyuNcDc_doCNho68bmSrrwmWEux4J15TrDD_6dVkDD4Q-RsXedplY8WcthMnlB0MkvEFl0pcQKPwUYBnkAvDODK_Na5BGkqlcmXVTpoaYYahwBifzuFrIy6axVbn8lhw6Y6tytDYli5gBVxUspp3m-0PoYO_X4bwuMN1wkknBEwkRf9bVhJnoNuGNK_NFURNtR6hO8NFf8cXYjt0u47-x5DOAq1UGpw0

HTML
https://hypothes.is/api/annotations/M7ilUoPoEeqkjdOD4PJfug

PDF
https://hypothes.is/api/annotations/YStnXoPoEeqBjddiX9JZmA

Hypothesis doesn’t connect these two references, note the PDF URL in metadata ends up being redirected and rewritten, so no obvious connection between two resources.

```
<meta name="citation_pdf_url" content="https://academic.oup.com/jmammal/article-pdf/89/4/815/2580856/89-4-815.pdf">
```




### PDFs

A big challenge will be having same text coordinates as hypothesis.is uses for PDFs. Perhaps we need a PDF.js server that returns text for each page, and we apply our markup code to that?









## Existing examples

### Paris museum

https://science.mnhn.fr/institution/mnhn/collection/im/item/2000-1820?listIndex=1&listCount=1102 includes links between specimen and literature 

See also specimen in GBIF https://www.gbif.org/occurrence/1019688622 with bibliographic data attached

### NHM

cf. Paris the specimen “NHMUK 1896.1.25.7-8” is cited in Fig 12 of https://zookeys.pensoft.net/article/28800 https://doi.org/10.3897/zookeys.834.28800 but NHM museum has no record of this (Paris a link from this paper to one of its specimens.

https://data.nhm.ac.uk/dataset/56e711e6-c847-4f99-915a-6894bb5c5dea/resource/05ff2255-c38a-40c9-b657-4ccb55ab2feb/record/3076567

**make a nice example for a paper**


## Examples

### A NEW SPECIES OF DIASTEMA (GESNERIACEAE) FROM THE EASTERN ANDEAN SLOPES OF PERU

[10.1017/S0960428619000192](https://doi.org/10.1017/S0960428619000192)

Specimen code [E00885503] is http://data.rbge.org.uk/herb/E00885503

### Review of Odontochrydium Brauns (Hymenoptera, Chrysididae) with description of two species from the Palaearctic and Oriental regions

10.11646/zootaxa.4450.4.3

Paper is in RG https://www.researchgate.net/publication/326633905_Review_of_Odontochrydium_Brauns_Hymenoptera_Chrysididae_with_description_of_two_species_from_the_Palaearctic_and_Oriental_regions as is author https://www.researchgate.net/profile/Paolo_Rosa2

NHMUK010812294 has picture 
http://gbif.org/occurrence/1826357959
http://data.nhm.ac.uk/specimen/3a37b195-95ce-40f2-82d6-4c4ac79c46b2

### Checklist of British and Irish Hymenoptera - Chalcidoidea and Mymarommatoidea

https://bdj.pensoft.net/articles.php?id=8013

Neochrysocharis formosa 
BMNH(E) 1414560 has picture in paper and GBIF
http://gbif.org/occurrence/1056324450
http://data.nhm.ac.uk/specimen/02b94da1-f048-4be8-8d9a-83b121da3507

### A revision and recircumscription of Begonia Section Pilderia including one new species

https://www.researchgate.net/profile/Orlando_Jara

Has empty ORCID (http://orcid.org/0000-0002-7123-124X) (confirmed by https://doi.org/10.1007/s12228-020-09605-0)

10.11646/phytotaxa.307.1.1

M.F. Gardner & S.G. Knees 6609 (E [E00131860])

[GBIF](https://www.gbif.org/occurrence/574947379)
[RBGE](http://data.rbge.org.uk/herb/E00131860)
[IIIF](https://iiif.rbge.org.uk/viewers/uv/index.php?manifest=https://iiif.rbge.org.uk/herb/iiif/E00131860/manifest)

[BM001006460] is a typo for [BM001008460]

### Dividing and conquering the fastest-growing genus: Towards a natural sectional classification of the mega-diverse genus Begonia (Begoniaceae)

Many sequences and specimens, POPSET https://www.ncbi.nlm.nih.gov/popset?LinkName=nuccore_popset&from_uid=1388809152

### Revision of Muhlenbergia (Poaceae, Chloridoideae, Cynodonteae, Muhlenbergiinae) in Peru: classification, phylogeny, and a new species, M. romaschenkoi

10.3897/phytokeys.114.28799

Treatment (one of many)
[plazi](http://tb.plazi.org/GgServer/html/EFFB8DA8E7EFA589053DDD6506FF24F1)

Specimen US-3730646 is [gbif:1320146812](https://www.gbif.org/occurrence/1320146812) = http://n2t.net/ark:/65665/382386b03-ba48-4e63-97d2-c43d0c6cd4aa GBIF has older id (doesn’t recognise it as a type of a new species)

### Names and types relating to the South American genus Lamanonia (Cunoniaceae) and its synonyms, the identity of L. speciosa, and an account of the little-known L. ulei

10.1007/S12225-017-9731-4 

ORCID for author, lots of specimen codes(barcodes), Kew Bulletin, open access, license and ORCID in CrossRef metadata.

## Ficopomatus talehsapensis, a new brackish-water species (Polychaeta: Serpulidae: Ficopomatinae) from Thailand, with discussions on the relationships of taxa constituting the subfamily, opercular insertion as a taxonomic character and their taxonomy, a key to its taxa, and their zoogeography

[10.11646/zootaxa.1967.1.2](https://doi.org/10.11646/zootaxa.1967.1.2)

BM (NH) 1938:5:7: 89–91
http://data.nhm.ac.uk/specimen/c80b3287-1e04-417b-8e1f-c7157ad46dfc
http://gbif.org/occurrence/1057440386
 
In GBIF as Ficopomatus macrodon, it’s actually a new species which GBIF has no records for.



## texas type catalogue

Mismatch between specimen codes in paper and GBIF, e.g.

Peltodytes dunavani Young, 1961 
TTU-Z030022 in paper p.26
TTU-Z_030022 in GBIf https://www.gbif.org/occurrence/2556261738
