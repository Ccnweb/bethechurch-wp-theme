const layoutStyles = [
    {
       'block': 'core/columns',
       'variants': [
            {
                name: 'restricted-width',
                label: 'Restricted Width', //_('Restricted Width', 'ccnmj'),
            },
            {
                name: 'cards',
                label: 'Cards', //_('Full Width', 'ccnmj'),
            },
            {
                name: 'squares',
                label: 'Carrés', //_('Restricted Width', 'ccnmj'),
            },
       ],
    },
    {
        'block': 'core/cover',
        'variants': [
            {
                name: 'standard',
                label: 'Standard',
                isDefault: false,
            },
            {
                name: 'full-height',
                label: 'Pleine hauteur',
                isDefault: true,
            }
        ],
    },
    {
        'block': 'core/quote',
        'variants': [
            {
                name: 'default',
                label: 'Par défaut',
                isDefault: true,
            },
            {
                name: 'large',
                label: 'Large',
                isDefault: false,
            },
            {
                name: 'animated',
                label: 'Animé',
                isDefault: false,
            }
        ],
    },
    {
        'block': 'core/heading',
        'variants': [
            {
                name: 'default',
                label: 'Normal',
                isDefault: true,
            },
            {
                name: 'accordion',
                label: 'Accordeon',
                isDefault: false,
            }
        ],
    },
    {
        'block': 'core/button',
        'variants': [
            {
                name: 'fill',
                label: 'Plein',
                isDefault: true,
            },
            {
                name: 'outline',
                label: 'Contour',
            },
            {
                name: 'underline',
                label: 'Souligné',
            },
            {
                name: 'round',
                label: 'Rond',
            },
        ],
    },
    {
        'block': 'core/image',
        'variants': [
            {
                name: 'cover',
                label: 'Cover / Couvrir',
                isDefault: true,
            }
        ],
    },
    {
        'block': 'core/list',
        'variants': [
            {
                name: 'standard',
                label: 'Standard',
                isDefault: true,
            },
            {
                name: 'colorful',
                label: 'Coloré',
            }
        ],
    },
]

const register = () => {
    layoutStyles.forEach( layoutStyle => layoutStyle.variants.forEach(variant => wp.blocks.registerBlockStyle(layoutStyle.block, variant)) )
}

register();