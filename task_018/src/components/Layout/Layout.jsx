import styled from 'styled-components';
import Sidebar from '../Sidebar/Sidebar';
import Navbar from '../Navbar/Navbar';
import { Outlet } from 'react-router-dom';

const AppContainer = styled.div`
  display: flex;
  min-height: 100vh;
`;

const MainContent = styled.main`
  flex: 1;
  margin-left: 260px; /* Width of the sidebar */
  display: flex;
  flex-direction: column;
  transition: margin-left 0.3s ease-in-out;

  @media (max-width: 768px) {
    margin-left: 0;
  }
`;

const PageContent = styled.div`
  padding: 0 30px 30px;
  flex: 1;

  @media (max-width: 768px) {
    padding: 0 10px 20px;
  }
`;

const Layout = ({ toggleTheme, isDarkMode }) => {
  return (
    <AppContainer>
      <Sidebar />
      <MainContent>
        <Navbar toggleTheme={toggleTheme} isDarkMode={isDarkMode} />
        <PageContent>
          <Outlet />
        </PageContent>
      </MainContent>
    </AppContainer>
  );
};

export default Layout;
