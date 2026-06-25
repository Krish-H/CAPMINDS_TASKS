import styled from 'styled-components';
import { Moon, Sun } from 'lucide-react';
import { useLocation } from 'react-router-dom';

const NavbarContainer = styled.nav`
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 20px 30px;
  background: ${({ theme }) => theme.colors.navbar};
  backdrop-filter: blur(10px);
  position: sticky;
  top: 0;
  z-index: 90;
  border-radius: ${({ theme }) => theme.borderRadius.lg};
  margin: 15px 30px;
  box-shadow: 0px 4px 15px rgba(0, 0, 0, 0.05);

  @media (max-width: 768px) {
    margin: 10px;
    padding: 15px 20px;
  }
`;

const PageTitle = styled.h2`
  font-size: 20px;
  font-weight: 700;
  color: ${({ theme }) => theme.colors.text};
  text-transform: capitalize;
`;

const ThemeToggle = styled.button`
  background: ${({ theme }) => theme.colors.surface};
  border: none;
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  color: ${({ theme }) => theme.colors.textSecondary};
  box-shadow: 0px 2px 5px rgba(0,0,0,0.05);
  transition: ${({ theme }) => theme.transitions.default};

  &:hover {
    color: ${({ theme }) => theme.colors.primary};
    transform: scale(1.05);
  }
`;

const Navbar = ({ toggleTheme, isDarkMode }) => {
  const location = useLocation();
  const path = location.pathname.split('/')[1] || 'Dashboard';

  return (
    <NavbarContainer>
      <PageTitle>{path}</PageTitle>
      <ThemeToggle onClick={toggleTheme}>
        {isDarkMode ? <Sun size={20} /> : <Moon size={20} />}
      </ThemeToggle>
    </NavbarContainer>
  );
};

export default Navbar;
