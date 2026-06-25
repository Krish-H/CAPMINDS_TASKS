import styled, { css } from 'styled-components';

const getVariantStyles = (variant, theme) => {
  switch (variant) {
    case 'secondary':
      return css`
        background: ${theme.colors.secondary};
        color: ${theme.colors.primary};
        &:hover {
          background: ${theme.colors.secondaryHover};
        }
      `;
    case 'danger':
      return css`
        background: ${theme.colors.danger};
        color: #fff;
        &:hover {
          background: ${theme.colors.dangerHover};
        }
      `;
    case 'primary':
    default:
      return css`
        background: ${theme.colors.primary};
        color: #fff;
        &:hover {
          background: ${theme.colors.primaryHover};
        }
      `;
  }
};

const StyledButton = styled.button`
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  border-radius: ${({ theme }) => theme.borderRadius.md};
  font-weight: 600;
  font-size: 14px;
  border: none;
  cursor: pointer;
  transition: ${({ theme }) => theme.transitions.default};
  outline: none;
  gap: 8px;

  ${({ variant, theme }) => getVariantStyles(variant, theme)}

  &:disabled {
    opacity: 0.6;
    cursor: not-allowed;
  }
`;

const Button = ({ children, variant = 'primary', icon: Icon, ...props }) => {
  return (
    <StyledButton variant={variant} {...props}>
      {Icon && <Icon size={18} />}
      {children}
    </StyledButton>
  );
};

export default Button;
