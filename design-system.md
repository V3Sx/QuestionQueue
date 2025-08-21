# Sistema de Design - Gerador de Perguntas

## Tipografia

### Fontes Utilizadas

#### 1. **Poppins** (Font-Heading)
- **Uso**: Títulos, subtítulos, botões e elementos de interface
- **Pesos**: 400, 500, 600, 700, 800
- **Justificativa**: Poppins é uma fonte moderna, geométrica e amigável que transmite profissionalismo e acessibilidade. Sua legibilidade é excelente em diferentes tamanhos, ideal para títulos e elementos de interface que precisam chamar atenção.

#### 2. **Inter** (Font-Sans)
- **Uso**: Texto do corpo, descrições, parágrafos e conteúdo geral
- **Pesos**: 400, 500, 600, 700
- **Justificativa**: Inter foi especificamente projetada para interfaces digitais, oferecendo excelente legibilidade em telas. É uma fonte neutra que não compete com os títulos em Poppins, criando uma hierarquia visual clara e harmoniosa.

### Hierarquia Tipográfica
- **H1 (Título Principal)**: Poppins Bold 3xl-4xl
- **H2 (Subtítulos)**: Poppins Semibold 2xl
- **H3 (Títulos de Cards)**: Poppins Semibold xl
- **H4 (Subtítulos de Features)**: Poppins Semibold
- **Perguntas**: Inter Medium 2xl-3xl (para melhor legibilidade)
- **Texto do Corpo**: Inter Regular
- **Botões**: Poppins Semibold

## Paleta de Cores

### Cores Principais

#### **Indigo/Azul** (Tema Principal)
- **Primary**: `#6366F1` (`hsl(235.4839, 91.6667%, 67.2549%)`)
  - **Hex**: #6366F1
  - **Justificativa**: O indigo transmite confiança, estabilidade e profissionalismo. É uma cor que inspira conversas profundas e reflexivas, ideal para um gerador de perguntas íntimas.

- **Primary Hover**: `#4F46E5` (`hsl(237.5, 82.6087%, 62.3529%)`)
  - **Hex**: #4F46E5
  - **Justificativa**: Versão mais escura para estados de hover, mantendo a identidade visual.

- **Primary Light**: `#EEF2FF` (`hsl(238.8889, 86.6667%, 95.2941%)`)
  - **Hex**: #EEF2FF
  - **Justificativa**: Versão clara para backgrounds e elementos sutis.

### Cores de Texto

#### **Text Primary**: `#1F2937` (`hsl(210, 25%, 12.1569%)`)
- **Hex**: #1F2937
- **Justificativa**: Cinza escuro que oferece excelente contraste e legibilidade sem ser preto puro, mais suave para os olhos.

#### **Text Secondary**: `#6B7280` (`hsl(210, 2.7027%, 42.7451%)`)
- **Hex**: #6B7280
- **Justificativa**: Cinza médio para textos secundários, mantendo hierarquia visual clara.

### Cores de Background

#### **Background Light**: `#F8FAFC` (`hsl(210, 16.6667%, 97.6471%)`)
- **Hex**: #F8FAFC
- **Justificativa**: Background muito claro com tom levemente azulado, criando harmonia com a paleta principal.

#### **Card Background**: `#FFFFFF`
- **Hex**: #FFFFFF
- **Justificativa**: Branco puro para cards, criando contraste e hierarquia visual.

### Cores de Apoio

#### **Border**: `#E2E8F0` (`hsl(201.4286, 30.4348%, 90.9804%)`)
- **Hex**: #E2E8F0
- **Justificativa**: Cinza azulado claro para bordas, mantendo consistência visual.

## Justificativas Gerais

### Escolha das Cores
1. **Psicologia**: O indigo/azul transmite confiança e serenidade, perfeito para conversas íntimas
2. **Acessibilidade**: Todas as combinações de cores atendem aos padrões WCAG AA de contraste
3. **Harmonia**: Paleta monocromática azul com neutros, criando unidade visual
4. **Modernidade**: Cores contemporâneas que não saem de moda rapidamente

### Escolha das Fontes
1. **Contraste Tipográfico**: Poppins (geométrica) vs Inter (humanista) cria hierarquia visual clara
2. **Legibilidade**: Ambas são otimizadas para telas digitais
3. **Personalidade**: Poppins adiciona personalidade moderna, Inter garante legibilidade
4. **Performance**: Fontes do Google Fonts carregam rapidamente e são confiáveis

## Implementação Técnica

### CSS Custom Properties
```css
:root {
  --font-heading: 'Poppins', sans-serif;
  --font-sans: 'Inter', sans-serif;
  --primary: hsl(235.4839 91.6667% 67.2549%);
  --primary-hover: hsl(237.5 82.6087% 62.3529%);
  --primary-light: hsl(238.8889 86.6667% 95.2941%);
}
```

### Tailwind Classes
- Títulos: `font-heading`
- Texto do corpo: `font-sans`
- Cores: `bg-primary`, `text-primary`, `bg-primary-light`