<?php

/**
 * This file will return the original response data of the H5P library
 * Accordion, which normally would be contained in:
 *
 * @see H5PCore::$librariesJsonData
 */

declare(strict_types=1);

return array(
    'FontAwesome 4.5' =>
        array(
            'title' => 'Font Awesome',
            'contentType' => 'Font',
            'majorVersion' => 4,
            'minorVersion' => 5,
            'patchVersion' => 4,
            'runnable' => 0,
            'machineName' => 'FontAwesome',
            'license' => 'MIT',
            'author' => 'Dave Gandy',
            'preloadedCss' =>
                array(
                    0 =>
                        array(
                            'path' => 'h5p-font-awesome.min.css',
                        ),
                ),
            'hasIcon' => false,
            'uploadDirectory' => 'data/default/h5p/temp/h5p-63c28a34d98e4/FontAwesome-4.5',
        ),
    'H5P.Accordion 1.0' =>
        array(
            'title' => 'Accordion',
            'majorVersion' => 1,
            'minorVersion' => 0,
            'patchVersion' => 30,
            'embedTypes' =>
                array(
                    0 => 'iframe',
                ),
            'runnable' => 1,
            'fullscreen' => 0,
            'machineName' => 'H5P.Accordion',
            'author' => 'Joubel',
            'coreApi' =>
                array(
                    'majorVersion' => 1,
                    'minorVersion' => 5,
                ),
            'license' => 'MIT',
            'preloadedJs' =>
                array(
                    0 =>
                        array(
                            'path' => 'h5p-accordion.js',
                        ),
                ),
            'preloadedDependencies' =>
                array(
                    0 =>
                        array(
                            'machineName' => 'FontAwesome',
                            'majorVersion' => 4,
                            'minorVersion' => 5,
                        ),
                ),
            'preloadedCss' =>
                array(
                    0 =>
                        array(
                            'path' => 'h5p-accordion.css',
                        ),
                ),
            'semantics' => '[
  {
    "name": "panels",
    "type": "list",
    "label": "Panels",
    "entity": "panel",
    "max": 100,
    "min": 1,
    "field": {
      "name": "content",
      "type": "group",
      "label": "Content",
      "importance": "high",
      "entity": "content",
      "fields": [
        {
          "name": "title",
          "type": "text",
          "label": "Title",
          "importance": "high"
        },
        {
          "name": "content",
          "type": "library",
          "label": "Content type",
          "importance": "medium",
          "entity": "content",
          "options": [
            "H5P.AdvancedText 1.1"
          ]
        }
      ]
    }
  },
  {
    "name": "hTag",
    "type": "select",
    "label": "H tags for labels (does not affect the size of the label)",
    "importance": "low",
    "description": "The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
    "options": [
      {
        "value": "h2",
        "label": "H2"
      },
      {
        "value": "h3",
        "label": "H3"
      },
      {
        "value": "h4",
        "label": "H4"
      }
    ],
    "default": "h2"
  }
]
',
            'language' =>
                array(
                    'af' => '{
  "semantics": [
    {
      "label": "Panele",
      "entity": "paneel",
      "field": {
        "label": "Inhoud",
        "entity": "inhoud",
        "fields": [
          {
            "label": "Titel"
          },
          {
            "label": "Inhoud tipe",
            "entity": "inhoud"
          }
        ]
      }
    },
    {
      "label": "H merker vir etiket (verander nie die grote van die etiket nie)",
      "description": "Die h-tag wat op die etikette gebruik word. Normaalweg H2, maar as dit onder \'n H2-opskrif hoort, gebruik H3. Beïnvloed nie die grootte van die etikette nie, slegs vir semantiese doeleindes.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'ar' => '{
  "semantics": [
    {
      "label": "لوحات",
      "entity": "لوحه",
      "field": {
        "label": "المحتوي",
        "entity": "المحتوي",
        "fields": [
          {
            "label": "عنوان فصل"
          },
          {
            "label": "نوع المحتوى",
            "entity": "المحتوى"
          }
        ]
      }
    },
    {
      "label": "”H tag“ للتسميات , ( لا يؤثر على حجم التسميات )",
      "description": "العلامة h المستخدمة على الملصقات. عادة”H“ ولكن إذا كان هذا ينتمي تحت عنوان ”H2“ استخدم” H3“. لا يؤثر على حجم الملصقات ، ويستخدم فقط للأغراض الدلالية.",
      "options": [
        {
          "label": "”H2“"
        },
        {
          "label": "”H3“"
        },
        {
          "label": "”H4“"
        }
      ]
    }
  ]
}
',
                    'bg' => '{
  "semantics": [
    {
      "label": "Панели",
      "entity": "panel",
      "field": {
        "label": "Съдържание",
        "entity": "content",
        "fields": [
          {
            "label": "Заглавие"
          },
          {
            "label": "Тип съдържание",
            "entity": "content"
          }
        ]
      }
    },
    {
      "label": "H тагове за етикети (не влияе на размера на етикета)",
      "description": "H таг се използва за етикетите. Обикновено е H2, но ако се задава под H2 заглавие, да се използва H3. Не влияе на размера на етикетите, използва се единствено за семантични цели.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'bs' => '{
  "semantics":[
    {
      "label":"Panels",
      "entity":"panel",
      "field":{
        "label":"Content",
        "entity":"content",
        "fields":[
          {
            "label":"Title"
          },
          {
            "label":"Content type",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H tags for labels (does not affect the size of the label)",
      "description":"The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'ca' => '{
  "semantics": [
    {
      "label": "Taulers",
      "entity": "tauler",
      "field": {
        "label": "Contingut",
        "entity": "contingut",
        "fields": [
          {
            "label": "Títol"
          },
          {
            "label": "Tipus de contingut",
            "entity": "contingut"
          }
        ]
      }
    },
    {
      "label": "Identificadors H per a les etiquetes (no afecta la mida de l’etiqueta)",
      "description": "Identificador H que s’utilitza a les etiquetes. Normalment és H2, però si es mostra a sota d’un encapçalament H2, cal fer servir H3. L’identificador no afecta la mida de les etiquetes; només té finalitats semàntiques.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'cs' => '{
  "semantics":[
    {
      "label":"Panely",
      "entity":"panel",
      "field":{
        "label":"Obsah",
        "entity":"obsah",
        "fields":[
          {
            "label":"Nadpis"
          },
          {
            "label":"Typ obsahu",
            "entity":"obsah"
          }
        ]
      }
    },
    {
      "label":"Tagy H pro popisky (nemá vliv na velikost štítku)",
      "description":"Tag h použitý na popisku. Normálně H2, ale pokud patří pod hlavičku H2, použijte H3. Nemá vliv na velikost popisku, které se používají pouze pro sémantické účel.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'da' => '{
  "semantics":[
    {
      "label":"Panels",
      "entity":"panel",
      "field":{
        "label":"Content",
        "entity":"content",
        "fields":[
          {
            "label":"Title"
          },
          {
            "label":"Content type",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H tags for labels (does not affect the size of the label)",
      "description":"The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'de' => '{
  "semantics": [
    {
      "label": "Abschnitte",
      "entity": "Abschnitt",
      "field": {
        "label": "Inhalt",
        "entity": "Inhalt",
        "fields": [
          {
            "label": "Titel"
          },
          {
            "label": "Inhaltstyp",
            "entity": "Inhalt"
          }
        ]
      }
    },
    {
      "label": "H-Tags für die Leisten (hat keinen Einfluss auf die Größe der Leiste)",
      "description": "Das H-Tag, das für die Leisten verwendet wird. Normalerweise H2, aber falls das Akkordeon unterhalb einer H2-Überschrift steht, kannst du H3 nutzen. Es hat keinen Einfluss auf die Größe der Leiste und wird nur für semantische Zwecke verwendet.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'el' => '{
  "semantics":[
    {
      "label":"Αναδιπλούμενα Πλαίσια",
      "entity":"πλαισιου",
      "field":{
        "label":"Περιεχόμενο",
        "entity":"περιεχομενου",
        "fields":[
          {
            "label":"Τίτλος"
          },
          {
            "label":"Τύπος περιεχομένου",
            "entity":"περιεχομενου"
          }
        ]
      }
    },
    {
      "label":"Ετικέτες h για επικεφαλίδες (δεν επηρεάζει το μέγεθος της επικεφαλίδας)",
      "description":"Η ετικέτα h που χρησιμοποιείται στις επικεφαλίδες. Κανονικά χρησιμοποιείται το H2 αλλά αν υπάγεται σε ένα H2 χρησιμοποιήστε το H3. Η ρύθμιση αυτή δεν επηρεάζει το μέγεθος των επικεφαλίδων, χρησιμοποιείται μόνο για σημασιολογικούς σκοπούς.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'es-mx' => '{
  "semantics": [
    {
      "label": "Paneles",
      "entity": "panel",
      "field": {
        "label": "Contenido",
        "entity": "contenido",
        "fields": [
          {
            "label": "Título"
          },
          {
            "label": "Tipo de contenido",
            "entity": "contenido"
          }
        ]
      }
    },
    {
      "label": "Valor HTML H para etiquetas (no afecta el tamaño de la etiqueta)",
      "description": "El valor HTML H usado en las etiquetas. Normalmente H2 pero si esto pertenece a un encabezado H2 usa H3. No afecta el tamaño de las etiquetas, solo se usa con fines semánticos.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'es' => '{
  "semantics": [
    {
      "label": "Paneles",
      "entity": "panel",
      "field": {
        "label": "Contenido",
        "entity": "contenido",
        "fields": [
          {
            "label": "Título"
          },
          {
            "label": "Tipo de contenido",
            "entity": "contenido"
          }
        ]
      }
    },
    {
      "label": "Valor HTML H para etiquetas (no afecta el tamaño de la etiqueta)",
      "description": "El valor HTML H usado en las etiquetas. Normalmente H2 pero si esto pertenece a un encabezado H2 usa H3. No afecta el tamaño de las etiquetas, solo se usa con fines semánticos.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'et' => '{
  "semantics":[
    {
      "label":"Paneelid",
      "entity":"paneel",
      "field":{
        "label":"Sisu",
        "entity":"sisu",
        "fields":[
          {
            "label":"Pealkiri"
          },
          {
            "label":"Sisu tüüp",
            "entity":"sisu"
          }
        ]
      }
    },
    {
      "label":"Pealkirja H tunnus (ei mõjuta pealkirja suurust)",
      "description":"Pealkirjade h tunnus. Tavaliselt H2, kuid alapealkirjade puhul kasuta H3. Ei mõjuta pealkirjade suurust, kasutatakse üksnes eristaval eesmärgil.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'eu' => '{
  "semantics": [
    {
      "label": "Panelak",
      "entity": "panela",
      "field": {
        "label": "Edukia",
        "entity": "edukia",
        "fields": [
          {
            "label": "Izenburua"
          },
          {
            "label": "Eduki mota",
            "entity": "edukia"
          }
        ]
      }
    },
    {
      "label": "H etiketentzako markak (ez dio eragiten etiketen tamainari)",
      "description": "Etiketetan erabiltzen den h marka. Normalean H2 baina H2 izenburupean badago erabili H3. Honek ez dio eragiten etiketaren tamainari, bakarrik erabiltzen da helburu semantikoekin.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'fa' => '{
  "semantics": [
    {
      "label": "پنل‌ها",
      "entity": "پنل",
      "field": {
        "label": "محتوا",
        "entity": "محتوا",
        "fields": [
          {
            "label": "عنوان"
          },
          {
            "label": "نوع محتوا",
            "entity": "محتوا"
          }
        ]
      }
    },
    {
      "label": "تگ‌های H برای برچسب‌ها (بر اندازه برچسب اثر نمی‌کند)",
      "description": "تگ‌های H مورد استفاده روی برچسب‌ها. به طور معمول از H2 استفاده کنید ولی اگر زیر یک سرتیتر H2 است از H3 استفاده کنید. بر اندازه برچسب‌ها اثر نمی‌کند، فقط برای اهداف معناشناختی مورد استفاده قرار می‌گیرد.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'fi' => '{
  "semantics":[
    {
      "label":"Paneelit",
      "entity":"paneeli",
      "field":{
        "label":"Sisältö",
        "entity":"sisältö",
        "fields":[
          {
            "label":"Otsikko"
          },
          {
            "label":"Sisältötyyppi",
            "entity":"sisältö"
          }
        ]
      }
    },
    {
      "label":"Otsikon H tagi (Ei vaikuta otsikon kokoon)",
      "description":"H tagiä käytetään otsikoille. Tyypillisesti H2, mutta jos pitää olla alempitasoinen niin käytä H3. Ei vaikuta otsikon kokoon, käytetty vain kuvaavassa tarkoituksessa.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'fr' => '{
  "semantics":[
    {
      "label":"Titre de section",
      "entity":"panel",
      "field":{
        "label":"Contenu",
        "entity":"content",
        "fields":[
          {
            "label":"Titre"
          },
          {
            "label":"Type de contenu",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"Titre de section en tag H (ne modifie pas la taille du bloc de l\'en-tête)",
      "description":"Le tag H définit le titre de chaque section qui est en H2 par défaut. Utiliser H3 si ce titre est imbriqué sous une section en H2. Ne modifie pas la taille du bloc de la section - c\'est une question de sémantique.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'gl' => '{
  "semantics": [
    {
      "label": "Paneis",
      "entity": "panel",
      "field": {
        "label": "Contido",
        "entity": "contido",
        "fields": [
          {
            "label": "Título"
          },
          {
            "label": "Tipo de contido",
            "entity": "contido"
          }
        ]
      }
    },
    {
      "label": "Tags html H para as etiquetas (non afecta ao tamaño da etiqueta)",
      "description": "O tag html H usado nas etiquetas. Normalmente H2 pero se o tag vai debaixo dun H2, usa H3. Non afecta ao tamaño das etiquetas e úsase só con finalidade semántica.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'he' => '{
  "semantics": [
    {
      "label": "פאנלים",
      "entity": "פאנל",
      "field": {
        "label": "תוכן",
        "entity": "תוכן",
        "fields": [
          {
            "label": "כותרת"
          },
          {
            "label": "סוג תוכן",
            "entity": "תוכן"
          }
        ]
      }
    },
    {
      "label": "H tags for labels (does not affect the size of the label)",
      "description": "The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'hu' => '{
  "semantics":[
    {
      "label":"Panels",
      "entity":"panel",
      "field":{
        "label":"Content",
        "entity":"content",
        "fields":[
          {
            "label":"Title"
          },
          {
            "label":"Content type",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H tags for labels (does not affect the size of the label)",
      "description":"The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'it' => '{
  "semantics": [
    {
      "label": "Pannelli",
      "entity": "pannello",
      "field": {
        "label": "Contenuto",
        "entity": "contenuto",
        "fields": [
          {
            "label": "Titolo"
          },
          {
            "label": "Tipo di contenuto",
            "entity": "contenuto"
          }
        ]
      }
    },
    {
      "label": "Tag Titolo per etichette (non influisce sulla dimensione dell\'etichetta)",
      "description": "Tag Titolo utilizzato sulle etichette. Di solito è Titolo 2, ma se viene dopo un Titolo 2 gli è assegnato Titolo 3. In ogni caso non influisce sulla dimensione delle etichette poiché è usato solo per scopi semantici.",
      "options": [
        {
          "label": "Titolo 2"
        },
        {
          "label": "Titolo 3"
        },
        {
          "label": "Titolo 4"
        }
      ]
    }
  ]
}
',
                    'ja' => '{
  "semantics": [
    {
      "label": "パネル",
      "entity": "パネル",
      "field": {
        "label": "コンテンツ",
        "entity": "コンテンツ",
        "fields": [
          {
            "label": "タイトル"
          },
          {
            "label": "コンテンツタイプ",
            "entity": "コンテンツ"
          }
        ]
      }
    },
    {
      "label": "ラベル用のHタグ（ラベルのサイズには影響しません）",
      "description": "hタグはラベルに用いられます。通常はH2ですが、これがH2の見出しより下になる場合にはH3を用います。 ラベルのサイズには影響しません。意味的な目的にのみ使われます。",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'ka' => '{
  "semantics": [
    {
      "label": "პანელი",
      "entity": "პანელი",
      "field": {
        "label": "შიგთავსი",
        "entity": "შიგთავსი",
        "fields": [
          {
            "label": "დასახელება"
          },
          {
            "label": "მასალის ნაირსახეობა",
            "entity": "მასალის ნაირსახეობა"
          }
        ]
      }
    },
    {
      "label": "H თეგები გამოიყენება ნიშნულებისთვის (არ მოქმედებს სათაურის ზომაზე)",
      "description": "\'h\' ტიპის თეგი გამოიყენება ნიშნულებისთვის. ჩვეულებრივ H2 თეგის ნიშნულს იყენებენ. თუმცა იმ შემთხვევაში, როდესაც თქვენი ნიშნულისთვის უკვე გამოიყენება H2, გამოიყენეთ H3. ამა თუ იმ h თეგის გამოყენება ნიშნულის ზომაზე არ მოქმედებს. მხოლოდ სემანტიკური მიზნებისთვის გამოიყენება.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'ko' => '{
  "semantics":[
    {
      "label":"패널",
      "entity":"패널",
      "field":{
        "label":"콘텐츠",
        "entity":"콘텐츠",
        "fields":[
          {
            "label":"제목"
          },
          {
            "label":"콘텐츠 유형",
            "entity":"콘텐츠"
          }
        ]
      }
    },
    {
      "label":"라벨을 위한 H 태그 (라벨 크기에는 영향을 주지 않음)",
      "description":"레이블에 사용된 H 태그. 일반적으로 H2이지만 H2 머리글 아래에 속할 경우 H3을 사용하십시오. 라벨의 크기에 영향을 주지 않으며, 단지 의미상의 목적으로만 사용됨",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'lt' => '{
  "semantics": [
    {
      "label": "Skydeliai",
      "entity": "skydelis",
      "field": {
        "label": "Turinys",
        "entity": "turinys",
        "fields": [
          {
            "label": "Pavadinimas"
          },
          {
            "label": "Turinio tipas",
            "entity": "turinys"
          }
        ]
      }
    },
    {
      "label": "Žymos antraštės dydis (neturi įtakos elementų dydžiui)",
      "description": "Antraštė - h naudojama žymoms. Numatyta, jog yra naudojama antraštė H2, bet jei žyma priklauso H2, naudokite H3. Antraštės neturi įtakos elementų dydžiui, tai naudojama tik semenatiniams tisklams.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'lv' => '{
  "semantics": [
    {
      "label": "Paneļi",
      "entity": "panelis",
      "field": {
        "label": "Saturs",
        "entity": "saturs",
        "fields": [
          {
            "label": "Nosaukums"
          },
          {
            "label": "Satura veids",
            "entity": "saturs"
          }
        ]
      }
    },
    {
      "label": "H birka etiķetei (neietekmē etiķetes izmēru)",
      "description": "H birka tiek izmantota etiķetēm. Parasti H2, taču, ja saturs jāizvieto zem H2, izmantojiet H3. Neietekmē etiķetes izmēru, tiek izmantots tikai semantiskiem mērķiem.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'mn' => '{
  "semantics": [
    {
      "label": "Самбар",
      "entity": "самбар",
      "field": {
        "label": "Контент",
        "entity": "контент",
        "fields": [
          {
            "label": "Гарчиг"
          },
          {
            "label": "Контентийн төрөл",
            "entity": "контент"
          }
        ]
      }
    },
    {
      "label": "H шошго (шошгоны хэмжээнд нөлөөлөхгүй)",
      "description": "Шошгон дээр ашигласан h шошго. Ихэвчлэн H2, гэхдээ энэ нь H2 гарчигт хамаарах бол H3-г ашиглана. Шошгоны хэмжээнд нөлөөлөхгүй, зөвхөн семантик зорилгоор ашигладаг.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'nb' => '{
  "semantics": [
    {
      "label": "Paneler",
      "entity": "panel",
      "field": {
        "label": "Innhold",
        "entity": "innhold",
        "fields": [
          {
            "label": "Tittel"
          },
          {
            "label": "Innholdstype",
            "entity": "innhold"
          }
        ]
      }
    },
    {
      "label": "Header tag for overskrifter (påvirker ikke størrelsen til etiketten)",
      "description": "H taggen brukes på overskrifter. Vanligvis er H2 korrekt, med mindre overskriften hører til under en H2 tag, bruk da H3. Påvirker ikke størrelsen på overskriftene, brukes bare for semantikk.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'nl' => '{
  "semantics": [
    {
      "label": "Panelen",
      "entity": "paneel",
      "field": {
        "label": "Inhoud",
        "entity": "inhoud",
        "fields": [
          {
            "label": "Titel"
          },
          {
            "label": "Inhoudstype",
            "entity": "inhoudstype"
          }
        ]
      }
    },
    {
      "label": "De kopmarkering voor titels (heeft geen invloed op de afmetingen van het label)",
      "description": "De H-markering wordt gebruikt voor koppen. Normaal gesproken H2, maar als die onder een H2 kop hoort, gebruik dan H3. Dit beïnvloedt de afmetingen van de labels niet, maar is enkel voor semantische doeleinden.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'nn' => '{
  "semantics":[
    {
      "label":"Panels",
      "entity":"panel",
      "field":{
        "label":"Content",
        "entity":"content",
        "fields":[
          {
            "label":"Title"
          },
          {
            "label":"Content type",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H tags for labels (does not affect the size of the label)",
      "description":"The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'pl' => '{
  "semantics":[
    {
      "label":"Panels",
      "entity":"panel",
      "field":{
        "label":"Content",
        "entity":"content",
        "fields":[
          {
            "label":"Title"
          },
          {
            "label":"Content type",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H tags for labels (does not affect the size of the label)",
      "description":"The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'pt-br' => '{
  "semantics": [
    {
      "label": "Painéis",
      "entity": "Painel",
      "field": {
        "label": "Conteúdo",
        "entity": "conteúdo",
        "fields": [
          {
            "label": "Título"
          },
          {
            "label": "Tipo de conteúdo",
            "entity": "conteúdo"
          }
        ]
      }
    },
    {
      "label": "Tags H para rótulos (não afeta o tamanho do rótulo)",
      "description": "A tag H é usada nos rótulos. Normalmente H2, mas se isto estiver abaixo de um cabeçalho H2 use H3. Não afeta o tamanho dos rótulos, usado apenas para propósitos semânticos.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'pt' => '{
  "semantics": [
    {
      "label": "Painéis",
      "entity": "painel",
      "field": {
        "label": "Conteúdo",
        "entity": "conteúdo",
        "fields": [
          {
            "label": "Título"
          },
          {
            "label": "Tipo de conteúdo",
            "entity": "conteúdo"
          }
        ]
      }
    },
    {
      "label": "Tags H para rótulos (não afeta o tamanho do rótulo)",
      "description": "A tag H usada nos rótulos. Normalmente H2, mas se isto estiver abaixo de um cabeçalho H2 use H3. Não afeta o tamanho dos rótulos, usado apenas para propósitos semânticos.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'ro' => '{
  "semantics":[
    {
      "label":"Panels",
      "entity":"panel",
      "field":{
        "label":"Content",
        "entity":"content",
        "fields":[
          {
            "label":"Title"
          },
          {
            "label":"Content type",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H tags for labels (does not affect the size of the label)",
      "description":"The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}',
                    'ru' => '{
  "semantics":[
    {
      "label":"Панели",
      "entity":"панель",
      "field":{
        "label":"Содержимое",
        "entity":"содержимое",
        "fields":[
          {
            "label":"Название"
          },
          {
            "label":"Тип материала",
            "entity":"Тип материала"
          }
        ]
      }
    },
    {
      "label":"H теги используются для меток (не влияют на размер заголовка)",
      "description":"Теги вида \'h\' используются в качестве меток. Обычно используют метку с тегом H2. Однако, в случае, если ваша метка уже находится под заголовком H2, используйте H3. Выбор того или иного тега h не влияет на размер меток, используется только в семантических целях.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'sl' => '{
  "semantics": [
    {
      "label": "Panoji",
      "entity": "panel",
      "field": {
        "label": "Vsebina",
        "entity": "content",
        "fields": [
          {
            "label": "Naslov"
          },
          {
            "label": "Tip vsebine",
            "entity": "content"
          }
        ]
      }
    },
    {
      "label": "H značke za oznake (ne vpliva na velikost oznak)",
      "description": "H značka, ki naj se uporabi za oznake. Običajno je to H2. Če je vsebina umeščena pod H2 naslov izberite H3. Izbira ne vpliva na velikost oznak, uporablja se zgolj za semantične namene.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'sma' => '{
  "semantics": [
    {
      "label": "Panels",
      "entity": "panel",
      "field": {
        "label": "Content",
        "entity": "content",
        "fields": [
          {
            "label": "Title"
          },
          {
            "label": "Content type",
            "entity": "content"
          }
        ]
      }
    },
    {
      "label": "H tags for labels (does not affect the size of the label)",
      "description": "The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}',
                    'sme' => '{
  "semantics": [
    {
      "label": "Panels",
      "entity": "panel",
      "field": {
        "label": "Content",
        "entity": "content",
        "fields": [
          {
            "label": "Title"
          },
          {
            "label": "Content type",
            "entity": "content"
          }
        ]
      }
    },
    {
      "label": "H tags for labels (does not affect the size of the label)",
      "description": "The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}',
                    'smj' => '{
  "semantics": [
    {
      "label": "Panels",
      "entity": "panel",
      "field": {
        "label": "Content",
        "entity": "content",
        "fields": [
          {
            "label": "Title"
          },
          {
            "label": "Content type",
            "entity": "content"
          }
        ]
      }
    },
    {
      "label": "H tags for labels (does not affect the size of the label)",
      "description": "The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}',
                    'sr' => '{
  "semantics":[
    {
      "label":"Панели",
      "entity":"panel",
      "field":{
        "label":"Садржај",
        "entity":"content",
        "fields":[
          {
            "label":"Наслов"
          },
          {
            "label":"Тип садржаја",
            "entity":"content"
          }
        ]
      }
    },
    {
      "label":"H ознаке за налепнице (не утиче на величину налепнице)",
      "description":"Ознака h се користи на налепницама. Уобичајено је H2, али ако ово спада под наслов H2, користите H3. Не утиче на величину налепница, користе се само у семантичке сврхе.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'sv' => '{
  "semantics": [
    {
      "label": "Paneler",
      "entity": "panel",
      "field": {
        "label": "Innehåll",
        "entity": "innehåll",
        "fields": [
          {
            "label": "Titel"
          },
          {
            "label": "Innehållstyp",
            "entity": "innehåll"
          }
        ]
      }
    },
    {
      "label": "H-taggar för etiketter (påverkar inte storleken på etiketten)",
      "description": "H-taggen som används för etiketter. Vanligtvis H2 men om denna hör hemma under en H2-rubrik så bör du använda H3. Påverkar inte storleken på etiketterna, används endast av semantiska skäl.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'te' => '{
  "semantics": [
    {
      "entity": "ప్యానెల్",
      "field": {
        "entity": "కంటెంట్",
        "fields": [
          {
            "label": "శీర్షిక"
          },
          {
            "label": "కంటెంట్ రకం",
            "entity": "కంటెంట్"
          }
        ],
        "label": "కంటెంట్"
      },
      "label": "ప్యానల్స్"
    },
    {
      "label": "లేబుల్స్ కొరకై H ట్యాగ్స్ (ఇది లేబుల్ సైజును మార్పు చేయదు)",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ],
      "description": "H ట్యాగ్స్ లేబుల్స్ కై వాడతారు. సాధారణంగా H2 కానీ ఇది H2 హెడ్డింగ్ కింద ఉన్నట్లైతే H3 వాడండి. ఇది లేబుల్స్ సైజును మార్పు చేయదు, సెమాంటిక్ ప్రయోజనాల కోసం మాత్రమే ఉపయోగించబడుతుంది."
    }
  ]
}
',
                    'th' => '{
  "semantics": [
    {
      "label": "แผงข้อมูล",
      "entity": "แผงข้อมูล",
      "field": {
        "label": "เนื้อหา",
        "entity": "เนื้อหา",
        "fields": [
          {
            "label": "ชื่อเรื่อง"
          },
          {
            "label": "ประเภทเนื้อหา",
            "entity": "เนื้อหา"
          }
        ]
      }
    },
    {
      "label": "แท็กหัวเรื่อง (H tag) สำหรับข้อความ (ไม่ส่งผลต่อขนาดข้อความ)",
      "description": "แท็กหัวเรื่อง (H tag) จะถูกใช้กับข้อความ ปกติจะเป็นรูปแบบ H2 โดยจะไม่ส่งผลต่อขนาดข้อความ แต่จะใช้เพื่อระบุความสำคัญในด้านระบบ",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'tr' => '{
  "semantics": [
    {
      "label": "Paneller",
      "entity": "panel",
      "field": {
        "label": "İçerik",
        "entity": "içerik",
        "fields": [
          {
            "label": "Başlık"
          },
          {
            "label": "İçerik türü",
            "entity": "içerik"
          }
        ]
      }
    },
    {
      "label": "Etiketler için H etiket değeri (etiketin boyutunu etkilemez)",
      "description": "Etiketler üzerinde kullanılan h etiketi. Normalde H2 kullanılır fakat H2 altındaysa bunu H3 olarak kullanın. Etiketlerin boyutunu etkilemeyecektir, sadece anlamsal amaçlar için kullanıldı.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'uk' => '{
  "semantics":[
    {
      "label":"Панелі",
      "entity":"панель",
      "field":{
        "label":"Вміст",
        "entity":"вміст",
        "fields":[
          {
            "label":"Назва"
          },
          {
            "label":"Тип матеріалу",
            "entity":"Тип матеріалу"
          }
        ]
      }
    },
    {
      "label":"H теги використовуються для міток (не впливають на розмір заголовка)",
      "description":"Теги виду \'h\' використовуються в якості міток. Зазвичай використовують мітку з тегом H2. Але якщо Ваша мітка вже знаходиться під заголовком H2, використовуйте H3. Вибір того чи іншого тега h не впливає на розмір міток, використовується тільки в семантичних цілях.",
      "options":[
        {
          "label":"H2"
        },
        {
          "label":"H3"
        },
        {
          "label":"H4"
        }
      ]
    }
  ]
}
',
                    'vi' => '{
  "semantics": [
    {
      "label": "Panels",
      "entity": "panel",
      "field": {
        "label": "Nội dung",
        "entity": "nội dung",
        "fields": [
          {
            "label": "Tiêu đề"
          },
          {
            "label": "Dạng nội dung",
            "entity": "nội dung"
          }
        ]
      }
    },
    {
      "label": "H tags for labels (does not affect the size of the label)",
      "description": "The h tag used on the labels. Normally H2 but if this belongs under an H2 heading use H3. Does not affect the size of the labels, only used for semantical purposes.",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'zh-cn' => '{
  "semantics": [
    {
      "label": "面板",
      "entity": "面板",
      "field": {
        "label": "内容",
        "entity": "内容",
        "fields": [
          {
            "label": "标题"
          },
          {
            "label": "内容类型",
            "entity": "内容"
          }
        ]
      }
    },
    {
      "label": "标题标签（不影响标签的大小）",
      "description": "标签使用的H标签。标签上使用的H标签。通常为H2，但如果属于H2标题，则使用H3。不影响标签的大小，仅用于语义目的。",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'zh-hans' => '{
  "semantics": [
    {
      "label": "所有区块",
      "entity": "区块",
      "field": {
        "label": "区块",
        "entity": "内容",
        "fields": [
          {
            "label": "标题"
          },
          {
            "label": "内容",
            "entity": "内容"
          }
        ]
      }
    },
    {
      "label": "字级设定",
      "description": "这个设定与显示的字体尺寸无关，只是用来作为语义比重用途，一般是用 H2（标题2），但如果在通篇内容有更重要的字是 H2 时，就可改成 H3，依此类推。",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                    'zh-hant' => '{
  "semantics": [
    {
      "label": "所有區塊",
      "entity": "panel",
      "field": {
        "label": "區塊",
        "entity": "content",
        "fields": [
          {
            "label": "標題"
          },
          {
            "label": "內容",
            "entity": "content"
          }
        ]
      }
    },
    {
      "label": "字級設定",
      "description": "這個設定與顯示的字體尺寸無關，只是用來作為語義比重用途，一般是用 H2（標題2），但如果在通篇內容有更重要的字是 H2 時，就可改成 H3，依此類推。 ",
      "options": [
        {
          "label": "H2"
        },
        {
          "label": "H3"
        },
        {
          "label": "H4"
        }
      ]
    }
  ]
}
',
                ),
            'hasIcon' => true,
            'uploadDirectory' => 'data/default/h5p/temp/h5p-63c28a34d98e4/H5P.Accordion-1.0',
        ),
    'H5P.AdvancedText 1.1' =>
        array(
            'title' => 'Text',
            'description' => 'A simple library that displays text with all kinds of styling.',
            'majorVersion' => 1,
            'minorVersion' => 1,
            'patchVersion' => 13,
            'runnable' => 0,
            'machineName' => 'H5P.AdvancedText',
            'author' => 'Joubel',
            'preloadedJs' =>
                array(
                    0 =>
                        array(
                            'path' => 'text.js',
                        ),
                ),
            'preloadedCss' =>
                array(
                    0 =>
                        array(
                            'path' => 'text.css',
                        ),
                ),
            'metadataSettings' =>
                array(
                    'disable' => 0,
                    'disableExtraTitleField' => 1,
                ),
            'semantics' => '[
  {
    "name": "text",
    "type": "text",
    "widget": "html",
    "label": "Text",
    "importance": "high",
    "enterMode": "p",
    "tags": [
      "strong",
      "em",
      "del",
      "a",
      "ul",
      "ol",
      "h2",
      "h3",
      "hr",
      "pre",
      "code"
    ],
    "font": {
      "size": true,
      "color": true,
      "background": true
    }
  }
]
',
            'language' =>
                array(
                    'af' => '{
  "semantics": [
    {
      "label": "Teks"
    }
  ]
}
',
                    'ar' => '{
  "semantics":[
    {
      "label":"النص"
    }
  ]
}',
                    'bg' => '{
  "semantics": [
    {
      "label": "Текст"
    }
  ]
}',
                    'ca' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'cs' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'da' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'de' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'el' => '{
  "semantics":[
    {
      "label":"Κείμενο"
    }
  ]
}
',
                    'es-mx' => '{
  "semantics": [
    {
      "label": "Texto"
    }
  ]
}
',
                    'es' => '{
  "semantics":[
    {
      "label":"Texto"
    }
  ]
}
',
                    'et' => '{
  "semantics":[
    {
      "label":"Tekst"
    }
  ]
}',
                    'eu' => '{
  "semantics": [
    {
      "label": "Testua"
    }
  ]
}
',
                    'fa' => '{
  "semantics": [
    {
      "label": "متن"
    }
  ]
}
',
                    'fi' => '{
  "semantics":[
    {
      "label":"Teksti"
    }
  ]
}',
                    'fr' => '{
  "semantics":[
    {
      "label":"Texte"
    }
  ]
}',
                    'gl' => '{
  "semantics": [
    {
      "label": "Texto"
    }
  ]
}
',
                    'he' => '{
  "semantics": [
    {
      "label": "תוכן"
    }
  ]
}
',
                    'hu' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'it' => '{
  "semantics":[
    {
      "label":"Testo"
    }
  ]
}',
                    'ja' => '{
  "semantics":[
    {
      "label":"テキスト"
    }
  ]
}',
                    'ka' => '{
  "semantics": [
    {
      "label": "ტექსტი"
    }
  ]
}
',
                    'km' => '{
  "semantics": [
    {
      "label": "អត្ថបទ"
    }
  ]
}
',
                    'ko' => '{
  "semantics":[
    {
      "label":"텍스트"
    }
  ]
}
',
                    'lv' => '{
  "semantics": [
    {
      "label": "Teksts"
    }
  ]
}
',
                    'mn' => '{
  "semantics": [
    {
      "label": "Текст"
    }
  ]
}
',
                    'nb' => '{
  "semantics":[
    {
      "label":"Tekst"
    }
  ]
}',
                    'nl' => '{
  "semantics": [
    {
      "label": "Tekst"
    }
  ]
}
',
                    'nn' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'pl' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'pt-br' => '{
  "semantics": [
    {
      "label": "Texto"
    }
  ]
}
',
                    'pt' => '{
  "semantics":[
    {
      "label":"Texto"
    }
  ]
}',
                    'ro' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'ru' => '{
  "semantics": [
    {
      "label": "Текст"
    }
  ]
}
',
                    'sl' => '{
  "semantics": [
    {
      "label": "Besedilo"
    }
  ]
}
',
                    'sma' => '{
  "semantics": [
    {
      "label": "Text"
    }
  ]
}
',
                    'sme' => '{
  "semantics": [
    {
      "label": "Text"
    }
  ]
}
',
                    'smj' => '{
  "semantics": [
    {
      "label": "Text"
    }
  ]
}
',
                    'sr' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'sv' => '{
  "semantics":[
    {
      "label":"Text"
    }
  ]
}',
                    'te' => '{
  "semantics": [
    {
      "label": "టెక్స్ట్"
    }
  ]
}
',
                    'th' => '{
  "semantics": [
    {
      "label": "ข้อความ"
    }
  ]
}
',
                    'tr' => '{
  "semantics": [
    {
      "label": "Yazı"
    }
  ]
}
',
                    'uk' => '{
  "semantics":[
    {
      "label":"Текст"
    }
  ]
}
',
                    'vi' => '{
  "semantics": [
    {
      "label": "Chữ"
    }
  ]
}
',
                ),
            'hasIcon' => false,
            'uploadDirectory' => 'data/default/h5p/temp/h5p-63c28a34d98e4/H5P.AdvancedText-1.1',
        ),
);
