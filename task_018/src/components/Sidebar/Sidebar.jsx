import { NavLink } from 'react-router-dom';
import styled from 'styled-components';
import { LayoutDashboard, Users, UserCircle } from 'lucide-react';

const SidebarContainer = styled.aside`
  background: ${({ theme }) => theme.colors.sidebar};
  width: 260px;
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  box-shadow: 14px 0px 40px rgba(112, 144, 176, 0.08);
  display: flex;
  flex-direction: column;
  padding: 30px 0;
  z-index: 100;
  transition: ${({ theme }) => theme.transitions.default};

  @media (max-width: 768px) {
    transform: translateX(-100%);
    /* You could add a prop like isOpen to slide it in */
  }
`;

const LogoContainer = styled.div`
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 40px;
`;

const LogoText = styled.h1`
  font-size: 24px;
  font-weight: 700;
  color: ${({ theme }) => theme.colors.text};
  
  span {
    color: ${({ theme }) => theme.colors.primary};
  }
`;

const Menu = styled.ul`
  list-style: none;
  display: flex;
  flex-direction: column;
  gap: 10px;
  padding: 0 20px;
`;

const MenuItem = styled.li`
  width: 100%;
`;

const StyledNavLink = styled(NavLink)`
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 12px 20px;
  border-radius: ${({ theme }) => theme.borderRadius.md};
  color: ${({ theme }) => theme.colors.textSecondary};
  font-weight: 600;
  font-size: 16px;
  transition: ${({ theme }) => theme.transitions.default};

  &:hover {
    background: ${({ theme }) => theme.colors.secondary};
    color: ${({ theme }) => theme.colors.primary};
  }

  &.active {
    background: ${({ theme }) => theme.colors.primary};
    color: #fff;
    box-shadow: 0px 4px 15px ${({ theme }) => theme.colors.primary}40;
  }
`;

const Sidebar = () => {
  return (
    <SidebarContainer>
      <LogoContainer>
        <LogoText>Dash<span>UI</span></LogoText>
      </LogoContainer>
      <Menu>
        <MenuItem>
          <StyledNavLink to="/" end>
            <LayoutDashboard size={20} />
            Dashboard
          </StyledNavLink>
        </MenuItem>
        <MenuItem>
          <StyledNavLink to="/users">
            <Users size={20} />
            Users
          </StyledNavLink>
        </MenuItem>
        <MenuItem>
          <StyledNavLink to="/profile">
            <UserCircle size={20} />
            Profile
          </StyledNavLink>
        </MenuItem>
      </Menu>
    </SidebarContainer>
  );
};

export default Sidebar;
