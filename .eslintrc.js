module.exports = {
  root: true,
  env: { browser: true, es2021: true, node: true },
  parser: '@typescript-eslint/parser',
  parserOptions: { ecmaVersion: 'latest', sourceType: 'module', ecmaFeatures: { jsx: true } },
  settings: { react: { version: 'detect' } },
  extends: [
    'eslint:recommended',
    'plugin:react/recommended',
    'plugin:react-hooks/recommended',
    'plugin:jsx-a11y/recommended',
    'plugin:import/recommended',
    'plugin:@typescript-eslint/recommended',
    'plugin:prettier/recommended'
  ],
  plugins: ['react','react-hooks','jsx-a11y','import','@typescript-eslint','prettier'],
  rules: {
    'prettier/prettier': ['error', { singleQuote: true, tabWidth: 2, useTabs: false }],
    quotes: ['error', 'single', { avoidEscape: true }],
    'react/react-in-jsx-scope': 'off',
    '@typescript-eslint/no-unused-vars': ['warn'],
    'import/order': ['error', { groups: [['builtin', 'external', 'internal']] }]
  }
};
